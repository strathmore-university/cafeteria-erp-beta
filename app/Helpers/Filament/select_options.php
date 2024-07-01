<?php

use App\Models\Inventory\Store;
use App\Models\Procurement\Supplier;

if ( ! function_exists('active_suppliers')) {
    function active_suppliers(): array
    {
        return Supplier::isActive()->select(['id', 'name'])->get()
            ->pluck('name', 'id')
            ->toArray();
    }
}

if ( ! function_exists('procurement_stores')) {
    /**
     * @throws Throwable
     */
    function procurement_stores(): array
    {
        $category = store_types('Procurement Store');

        return Store::isActive()->where('category_id', $category->id)
            ->select(['id', 'name'])->get()
            ->pluck('name', 'id')
            ->toArray();
    }
}
