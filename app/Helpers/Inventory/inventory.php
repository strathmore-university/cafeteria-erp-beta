<?php

use App\Models\Inventory\Article;
use App\Models\Inventory\Store;
use App\Support\Inventory\FetchArticleCapacity;
use App\Support\Inventory\FetchArticleUnits;
use App\Support\Inventory\UpdateStockLevel;

if ( ! function_exists('article_units')) {
    /**
     * @throws Throwable
     */
    function article_units(
        Article $article,
        ?Store $store = null,
        bool $soldStock = false
    ): int|float {
        return (new FetchArticleUnits())
            ->index($article, $store, $soldStock);
    }
}

if ( ! function_exists('article_capacity')) {
    /**
     * @throws Throwable
     */
    function article_capacity(
        Article $article,
        ?Store $store = null,
        bool $soldStock = false
    ): int|float {
        return (new FetchArticleCapacity())
            ->index($article, $store, $soldStock);
    }
}

if ( ! function_exists('update_stock_level')) {
    function update_stock_level(): UpdateStockLevel
    {
        return new UpdateStockLevel();
    }
}
