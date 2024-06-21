<?php

namespace App\Actions\Procurement;

use App\Models\Procurement\CreditNote;
use App\Models\Procurement\CreditNoteItem;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseOrderItem;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class IssueCrn
{
    private CreditNote $crn;

    public function execute(CreditNote $crn): void
    {
        $this->crn = $crn;

//        try {
//            DB::transaction(function (): void {
                $items = $this->crnItems();
                $items->each(fn($item) => $this->processCrnItem($item));

                $total = $items->sum('total_value');
                $this->updatePurchaseOrder();
                $this->updateCrn($total);

                CreditNoteItem::where('units', '<=', 0)
                    ->whereCreditNoteId($this->crn->id)
                    ->delete();

                success('Credit note successfully!');
//            });
//        } catch (Throwable $exception) {
//            error_notification($exception);
//        }
//
//        redirect(get_record_url($this->crn));
    }

    /**
     * @throws Throwable
     */
    public function processCrnItem(CreditNoteItem $item): void
    {
        $id = $item->purchase_order_item_id;
        $select = ['id', 'remaining_units', 'ordered_units', 'total_value', 'price'];

        $poItem = PurchaseOrderItem::whereId($id)->select($select)->first();
        $poItem->remaining_units -= $item->units;
        $poItem->ordered_units -= $item->units;
        $poItem->total_value = $poItem->ordered_units * $poItem->price;
        $poItem->update();
    }

    /**
     * @throws Throwable
     */
    private function crnItems(): Collection
    {
        $with = ['article:id,name,lifespan_days,valuation_rate,is_reference,team_id'];

        $items = CreditNoteItem::with($with)
            ->where('units', '>', 0)
            ->whereCreditNoteId($this->crn->id)
            ->get();

        $message = 'There are no items to be written off!';
        throw_if(!$items->count(), new Exception($message));

        return $items;
    }

    /**
     * @throws Throwable
     */
    private function updateCrn(float $total): void
    {
        $this->crn->issued_by = auth_id();
        $this->crn->total_value = $total;
        $this->crn->issued_at = now();
        $this->crn->updateStatus();
        $this->crn->update();
    }

    /**
     * @throws Throwable
     */
    private function updatePurchaseOrder(): void
    {
        $select = [
            'id', 'is_fulfilled', 'delivered_at', 'status', 'total_value'
        ];
        $purchaseOrder = PurchaseOrder::whereId($this->crn->purchase_order_id)
            ->select($select)
            ->first();

        if ($purchaseOrder->isFulfilled()) {
            $purchaseOrder->delivered_at = now();
            $purchaseOrder->is_fulfilled = true;
            $purchaseOrder->updateStatus();
        }

        $total = PurchaseOrderItem::wherePurchaseOrderId($purchaseOrder->id)
            ->sum('total_value');

        $purchaseOrder->total_value = $total;
        $purchaseOrder->update();
    }
}
