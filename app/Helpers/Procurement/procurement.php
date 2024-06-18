<?php

use App\Models\Procurement\PriceQuote;

if ( ! function_exists('query_price_quotes')) {
    function query_price_quotes(
        string|int $articleId,
        string|int $supplierId
    ): ?float {
        $id = (int) $articleId;
        $price = PriceQuote::where('article_id', '=', $id)
            ->whereSupplierId($supplierId)
            ->first()
            ?->price;

        return $price ?? null;
    }
}
