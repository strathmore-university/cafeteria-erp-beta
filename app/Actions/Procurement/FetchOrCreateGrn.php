<?php

namespace App\Actions\Procurement;

use App\Models\Procurement\GoodsReceivedNote;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseOrderItem;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class FetchOrCreateGrn
{
    /**
     * @throws Throwable
     */
    public function execute(
        PurchaseOrder $purchaseOrder
    ): ?GoodsReceivedNote {
        try {
            $message = 'Purchase order has already been fulfilled!';
            throw_if($purchaseOrder->isFulfilled(), new Exception($message));

            $message = 'Purchase order is no-longer valid!';
            throw_if( ! $purchaseOrder->isValidLPO(), new Exception($message));

            $grn = GoodsReceivedNote::wherePurchaseOrderId($purchaseOrder->id)
                ->where('status', '=', 'draft')
                ->first();

            return match (filled($grn)) {
                false => $this->createGrn($purchaseOrder),
                true => $grn,
            };
        } catch (Throwable $exception) {
            error_notification($exception);
        }

        return null;
    }

    /**
     * @throws Throwable
     */
    private function createGrn(
        PurchaseOrder $purchaseOrder
    ): ?GoodsReceivedNote {
        $grn = DB::transaction(function () use ($purchaseOrder) {
            $grn = $purchaseOrder->goodsReceivedNotes()->create([
                'supplier_id' => $purchaseOrder->supplier_id,
                'store_id' => $purchaseOrder->store_id,
                'created_by' => auth_id(),
            ]);

            $items = collect();
            $remainingItems = $purchaseOrder->remainingItems();
            $remainingItems->each(function (PurchaseOrderItem $item) use ($items): void {
                $items->push([
                    'article_id' => $item->getAttribute('article_id'),
                    'purchase_order_id' => $item->purchase_order_id,
                    'purchase_order_item_id' => $item->id,
                    'units' => $item->remaining_units,
                    'price' => $item->price,
                ]);
            });

            $total = $remainingItems->sum('total_value');
            $grn->items()->createMany($items->toArray());
            $grn->update(['total_value' => $total]);

            return $grn;
        });

        return tannery(filled($grn), $grn, null);
    }
}
