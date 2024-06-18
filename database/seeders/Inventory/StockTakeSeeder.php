<?php

namespace Database\Seeders\Inventory;

use App\Models\Inventory\StockTakeItem;
use App\Models\Inventory\Store;
use Illuminate\Database\Seeder;

class StockTakeSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::first();

        // no change in the stock levels
        $stockTake = $store->performStockTake();
        $stockTake->adjustStock();

        // increase stock levels
        $stockTake = $store->performStockTake();
        StockTakeItem::whereStockTakeId($stockTake->id)
            ->update(['actual_units' => 100]);
        $stockTake->adjustStock();

        // decrease stock levels
        $stockTake = $store->performStockTake();
        StockTakeItem::whereStockTakeId($stockTake->id)
            ->update(['actual_units' => 0]);
        $stockTake->adjustStock();
    }
}
