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

        //         increase stock levels
        $stockTake = $store->performStockTake();
        $items = StockTakeItem::whereStockTakeId($stockTake->id)->get();
        $items->each(fn (StockTakeItem $item) => $item->update(['actual_units' => 100]));
        $stockTake->adjustStock();

        return;

        // decrease stock levels
        $stockTake = $store->performStockTake();
        $items = StockTakeItem::whereStockTakeId($stockTake->id)->get();
        $items->each(fn (StockTakeItem $item) => $item->update(['actual_units' => 0]));
        $stockTake->adjustStock();

        //         increase stock levels
        $stockTake = $store->performStockTake();
        $items = StockTakeItem::whereStockTakeId($stockTake->id)->get();
        $items->each(fn (StockTakeItem $item) => $item->update(['actual_units' => 100]));
        $stockTake->adjustStock();
        // todo: look into the error thrown when the stock levels are increased ofter reducing to 0
    }
}
