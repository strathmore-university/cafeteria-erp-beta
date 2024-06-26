<?php

namespace App\Actions\Procurement\Crn;

use App\Models\Procurement\CreditNote;
use App\Models\Procurement\CreditNoteItem;
use App\Models\Procurement\PurchaseOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class GenerateCrn
{
    /**
     * @throws Throwable
     */
    public function execute(
        PurchaseOrder $purchaseOrder
    ): ?CreditNote {
        try {
            return DB::transaction(function () use ($purchaseOrder) {
                $message = 'Purchase order has already been fulfilled!';
                fire($purchaseOrder->isFulfilled(), $message);

                $crn = CreditNote::wherePurchaseOrderId($purchaseOrder->id)
                    ->where('status', '=', 'draft')
                    ->first();

                return match (filled($crn)) {
                    false => $this->createCrn($purchaseOrder),
                    true => $crn,
                };
            });
        } catch (Throwable $exception) {
            error_notification($exception);
            return null;
        }
    }

    /**
     * @throws Throwable
     */
    private function createCrn(PurchaseOrder $order): CreditNote
    {
        $crn = $order->creditNotes()->create([
            'supplier_id' => $order->supplier_id,
            'created_by' => auth_id(),
        ]);

        $remainingItems = $order->remainingItems();
        $items = $this->populateItems($remainingItems, $crn);

        CreditNoteItem::insert($items->toArray());
        $total = $remainingItems->sum('total_value');
        $crn->update(['total_value' => $total]);

        success('Credit Note generated successfully');

        return $crn;
    }

    private function populateItems(
        Collection $remainingItems,
        CreditNote $crn
    ): Collection {
        $items = collect();
        $remainingItems->each(function ($item) use ($crn, $items): void {
            $items->push([
                'article_id' => $item->getAttribute('article_id'),
                'purchase_order_id' => $item->purchase_order_id,
                'purchase_order_item_id' => $item->id,
                'units' => $item->remaining_units,
                'credit_note_id' => $crn->id,
                'price' => $item->price,
            ]);
        });

        return $items;
    }
}
