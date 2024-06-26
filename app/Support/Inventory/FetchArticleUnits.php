<?php

namespace App\Support\Inventory;

use App\Models\Inventory\Article;
use App\Models\Inventory\StockLevel;
use App\Models\Inventory\Store;
use Exception;
use Throwable;

class FetchArticleUnits
{
    private ?Store $store = null;

    /**
     * @throws Throwable
     */
    public function index(
        Article $article,
        ?Store $store = null,
        bool $soldStock = false
    ): float|int {
        $this->store = $store;

        $message = 'Cannot get count for reference articles';
        $check = $article->getAttribute('is_reference');
        throw_if((bool) $check, new Exception($message));

        //        todo: when batches expire, update stock level

        $id = $article->getAttribute('team_id');

        return StockLevel::where('article_id', '=', $article->id)
            ->where('is_sold_stock', '=', $soldStock)
            ->where('team_id', '=', $id)
            ->when(filled($this->store), function ($query): void {
                $query->where('store_id', '=', $this->store->id);
            })
            ->select(['current_units'])
            ->sum('current_units');
    }
}
