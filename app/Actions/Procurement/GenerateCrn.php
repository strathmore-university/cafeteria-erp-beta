<?php

namespace App\Actions\Procurement;

use App\Models\Procurement\CreditNote;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseOrderItem;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class GenerateCrn
{
    /**
     * @throws Throwable
     */
    public function execute(
        PurchaseOrder $purchaseOrder
    ): CreditNote {
        //        try {
        //            DB::transaction(function () use ($purchaseOrder) {
        $message = 'Purchase order has already been fulfilled!';
        throw_if($purchaseOrder->isFulfilled(), new Exception($message));

        $crn = CreditNote::wherePurchaseOrderId($purchaseOrder->id)
            ->where('status', '=', 'draft')
            ->first();

        return match (filled($crn)) {
            false => $this->createCrn($purchaseOrder),
            true => $crn,
        };
        //            });
        //        } catch (Throwable $exception) {
        //            error_notification($exception);
        //        }

        //        return null;
    }

    /**
     * @throws Throwable
     */
    private function createCrn(
        PurchaseOrder $purchaseOrder
    ): CreditNote {
        $crn = DB::transaction(function () use ($purchaseOrder) {
            $crn = $purchaseOrder->creditNotes()->create([
                'supplier_id' => $purchaseOrder->supplier_id,
                'created_by' => auth_id(),
            ]);

            $items = collect();
            $remainingItems = $purchaseOrder->remainingItems();
            $remainingItems->each(function (PurchaseOrderItem $item) use ($items): void {
                $id = $item->getAttribute('article_id');

                $items->push([
                    'purchase_order_id' => $item->purchase_order_id,
                    'purchase_order_item_id' => $item->id,
                    'units' => $item->remaining_units,
                    'price' => $item->price,
                    'article_id' => $id,
                ]);
            });

            $total = $remainingItems->sum('total_value');
            $crn->items()->createMany($items->toArray());
            $crn->update(['total_value' => $total]);

            success('Credit Note generated successfully');

            return $crn;
        });

        return tannery(filled($crn), $crn, null);
    }
}
