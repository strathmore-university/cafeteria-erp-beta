<?php

namespace App\Actions\Procurement\Grn;

use App\Models\Procurement\GoodsReceivedNote;
use App\Models\Procurement\GoodsReceivedNoteItem;
use App\Models\Procurement\PurchaseOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class FetchOrCreateGrn
{
    /**
     * @throws Throwable
     */
    public function execute(PurchaseOrder $order): ?GoodsReceivedNote
    {
        try {
            return DB::transaction(function () use ($order) {
                $this->validate($order);

                $grn = GoodsReceivedNote::wherePurchaseOrderId($order->id)
                    ->where('status', '=', 'draft')
                    ->first();

                return match (filled($grn)) {
                    false => $this->createGrn($order),
                    true => $grn,
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
    private function validate(PurchaseOrder $order): void
    {
        $message = 'Purchase order has already been fulfilled!';
        fire($order->isFulfilled(), $message);

        $message = 'Purchase order is no-longer valid!';
        fire( ! $order->isValidLPO(), $message);
    }

    /**
     * @throws Throwable
     */
    private function createGrn(PurchaseOrder $order): GoodsReceivedNote
    {
        $grn = $order->goodsReceivedNotes()->create([
            'supplier_id' => $order->supplier_id,
            'store_id' => $order->store_id,
            'created_by' => auth_id(),
        ]);

        $remainingItems = $order->remainingItems();
        $this->populateItems($grn, $remainingItems);

        $total = $remainingItems->sum('total_value');
        $grn->update(['total_value' => $total]);

        return $grn;
    }

    private function populateItems(
        GoodsReceivedNote $note,
        Collection $POItems
    ): void {
        $items = collect();
        $POItems->each(function ($item) use ($note, $items): void {
            $items->push([
                'total_value' => $item->price * $item->remaining_units,
                'article_id' => $item->getAttribute('article_id'),
                'purchase_order_id' => $item->purchase_order_id,
                'purchase_order_item_id' => $item->id,
                'goods_received_note_id' => $note->id,
                'units' => $item->remaining_units,
                'price' => $item->price,
            ]);
        });

        GoodsReceivedNoteItem::insert($items->toArray());
    }
}
