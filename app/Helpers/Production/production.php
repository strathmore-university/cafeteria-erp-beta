<?php

use App\Models\Inventory\Article;

if (! function_exists('product_articles')) {
    function product_articles(): array
    {
        return Article::isProduct()->isDescendant()
            ->pluck('name', 'id')
            ->toArray();
    }
}

if (! function_exists('ingredient_articles')) {
    function ingredient_articles(): array
    {
        return Article::whereIsIngredient(true)->isReference()
            ->pluck('name', 'id')->toArray();
    }
}
