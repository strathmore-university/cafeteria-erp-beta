<?php

namespace App\Actions\Procurement\Grn;

use App\Actions\Procurement\Grn\After\CreatePaymentVoucher;
use App\Actions\Procurement\Grn\After\CreateStockTransferRequest;
use App\Actions\Procurement\Grn\After\UpdatePurchaseOrderIfComplete;
use App\Models\Procurement\GoodsReceivedNote;
use App\Models\Procurement\GoodsReceivedNoteItem;
use Illuminate\Support\Collection;
use Throwable;

class PostGrnReceipt
{
    /**
     * @throws Throwable
     */
    public function execute(
        GoodsReceivedNote $grn,
        Collection $items,
    ): void {
        $id = $grn->purchase_order_id;
        (new UpdatePurchaseOrderIfComplete())->execute($id);

        GoodsReceivedNoteItem::where('units', '<=', 0)
            ->where('goods_received_note_id', '=', $grn->id)
            ->delete();

        (new CreatePaymentVoucher())->execute($grn, $items);

        (new CreateStockTransferRequest())->execute($grn, $items);
    }
}
