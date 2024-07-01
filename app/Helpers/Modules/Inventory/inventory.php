<?php

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Inventory\Store;
use App\Services\Inventory\AddStock;
use App\Services\Inventory\CreateMovements;
use App\Services\Inventory\MoveStock;
use App\Services\Inventory\UpdateStockLevel;
use App\Support\Inventory\FetchArticleCapacity;
use App\Support\Inventory\FetchArticleUnits;
use App\Support\Inventory\QuantityConverter;

if ( ! function_exists('quantity_converter')) {
    /**
     * @throws Throwable
     */
    function quantity_converter(
        Unit|int $from,
        Unit|int $to,
        float $value
    ): float {
        return (new QuantityConverter())->index($from, $to, $value);
    }
}

if ( ! function_exists('article_units')) {
    /**
     * @throws Throwable
     */
    function article_units(
        Article $article,
        ?Store $store = null,
        bool $soldStock = false
    ): int|float {
        return (new FetchArticleUnits())->index($article, $store, $soldStock);
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

if ( ! function_exists('create_movements')) {
    function create_movements(): CreateMovements
    {
        return new CreateMovements();
    }
}

if ( ! function_exists('move_stock')) {
    function move_stock(): MoveStock
    {
        return new MoveStock();
    }
}

if ( ! function_exists('add_stock')) {
    function add_stock(): AddStock
    {
        return new AddStock();
    }
}
