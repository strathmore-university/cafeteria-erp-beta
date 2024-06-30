<?php

namespace App\Actions\Retail;

use App\Models\Inventory\Store;
use App\Models\Production\Restaurant;
use App\Models\Retail\Sale;
use App\Models\Retail\SaleItem;
use App\Services\Inventory\CreateMovements;
use App\Services\Inventory\MoveStock;
use Throwable;

class ReduceStock
{
    private CreateMovements $movements;

    private MoveStock $moveStock;

    /**
     * @throws Throwable
     */
    public function execute(Sale $sale): void
    {
        $saleItems = SaleItem::with(['menuItem.article'])
            ->whereSaleId($sale->id)
            ->first();

        $this->movements = create_movements();
        $session = retail_session();

        $restaurant = Restaurant::find($session->restaurant_id);
        $stores = Store::with(['stockLevels', 'batches'])
            ->whereOwnerType($restaurant->getMorphClass())
            ->whereOwnerId($restaurant->id)
            ->select(['name', 'id', 'can_ship_stock', 'is_default'])
            ->get();

        $to = $stores->firstWhere('can_ship_stock', '=', false);
        $from = $stores->firstWhere('is_default', '=', true);

        $this->moveStock = move_stock()->movement($this->movements);
        $this->moveStock = $this->moveStock->from($from)->to($to);

        $saleItems->each(
            /**
             * @throws Throwable
             */
            function (SaleItem $item) use ($from): void {
                $article = $item->menuItem->article;

                $batches = $from->batches
                    ->where('article_id', '=', $article->id)
                    ->where('current_units', '>', 0);

                $this->movements = $this->moveStock->batches($batches)
                    ->units((int) $item->units)
                    ->article($article)
                    ->execute();
            }
        );

        $this->movements->execute();
        $this->moveStock->clear();
    }
}
