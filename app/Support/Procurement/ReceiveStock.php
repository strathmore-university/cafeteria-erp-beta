<?php

namespace App\Support\Procurement;

use App\Models\Inventory\Batch;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\Store;
use App\Models\Procurement\GoodsReceivedNoteItem;

class ReceiveStock
{
    public function index(GoodsReceivedNoteItem $item, Store $store): void
    {
        // create a batch
        $batch = Batch::create([

        ]);

        // create a movement
        StockMovement::create([

        ]);

        // update stock level

        // update the po item
        $poItem = $item->purchaseOrderItem;
        $poItem->remaining_units -= $item->units;
        $poItem->update();

        // update price quote table
    }
}
