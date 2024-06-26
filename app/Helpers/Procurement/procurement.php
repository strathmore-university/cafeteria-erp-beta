<?php

use App\Models\Inventory\Article;
use App\Models\Procurement\GoodsReceivedNoteItem;
use App\Models\Procurement\PriceQuote;
use App\Models\Procurement\PurchaseOrderItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\Model;

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

if ( ! function_exists('articles_that_can_be_added')) {
    function articles_that_can_be_added(string|int $id): array
    {
        $addedIds = PurchaseOrderItem::wherePurchaseOrderId($id)
            ->select('article_id')
            ->pluck('article_id')
            ->toArray();

        return Article::canBeOrdered()
            ->whereNotIn('id', $addedIds)
            ->select(['name', 'id'])
            ->pluck('name', 'id')
            ->toArray();
    }
}

if ( ! function_exists('grn_item_numeric_column')) {
    function grn_item_numeric_column(
        GoodsReceivedNoteItem|Model $grn,
        string $column
    ) {
        if ($grn->getAttribute('status') === 'received') {
            return TextColumn::make($column)->numeric()
                ->searchable()->sortable();
        }

        return TextInputColumn::make($column)->rules(['numeric', 'required']);
    }
}

if ( ! function_exists('grn_item_string_column')) {
    function grn_item_string_column(
        GoodsReceivedNoteItem|Model $grn,
        string $column
    ) {
        if ($grn->getAttribute('status') === 'received') {
            return TextColumn::make($column)->searchable();
        }

        return TextInputColumn::make($column)
            ->rules(['nullable', 'string', 'max:255']);
    }
}

if ( ! function_exists('grn_item_date_column')) {
    function grn_item_date_column(
        GoodsReceivedNoteItem|Model $grn,
        string $column
    ) {
        if ($grn->getAttribute('status') === 'received') {
            return TextColumn::make($column)->searchable()->sortable()
                ->formatStateUsing(fn ($state) => $state->diffForHumans());
        }

        return TextInputColumn::make($column)
            ->placeholder('yyyy-mm-dd')
            ->rules(['nullable', 'date', 'after:today']);
    }
}
