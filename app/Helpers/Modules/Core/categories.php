<?php

use App\Models\Core\Category;
use App\Support\Core\CategoryDirectory;
use Illuminate\Support\Collection;

if ( ! function_exists('account_types')) {
    /**
     * @throws Throwable
     */
    function account_types(string $search = ''): Collection|Category
    {
        $name = 'Account Types';

        return (new CategoryDirectory())->index($name, $search);
    }
}

if ( ! function_exists('accounting_categories')) {
    /**
     * @throws Throwable
     */
    function accounting_categories(string $search = ''): Collection|Category
    {
        $name = 'Accounting Categories';

        return (new CategoryDirectory())->index($name, $search);
    }
}

if ( ! function_exists('balance_types')) {
    /**
     * @throws Throwable
     */
    function balance_types(string $search = ''): Collection|Category
    {
        $name = 'Balance Types';

        return (new CategoryDirectory())->index($name, $search);
    }
}

if ( ! function_exists('store_types')) {
    /**
     * @throws Throwable
     */
    function store_types(string $search = ''): Collection|Category
    {
        $name = 'Store Types';

        return (new CategoryDirectory())->index($name, $search);
    }
}

if ( ! function_exists('disposal_types')) {
    /**
     * @throws Throwable
     */
    function disposal_types(string $search = ''): Collection|Category
    {
        $name = 'Disposal Types';

        return (new CategoryDirectory())->index($name, $search);
    }
}

if ( ! function_exists('object_types')) {
    /**
     * @throws Throwable
     */
    function object_types(string $search = ''): Collection|Category
    {
        $name = 'Object Types';

        return (new CategoryDirectory())->index($name, $search);
    }
}

if ( ! function_exists('organization_types')) {
    /**
     * @throws Throwable
     */
    function organization_types(string $search = ''): Collection|Category
    {
        $name = 'Organization Types';

        return (new CategoryDirectory())->index($name, $search);
    }
}

if ( ! function_exists('recipe_groups')) {
    /**
     * @throws Throwable
     */
    function recipe_groups(string $search = ''): Collection|Category
    {
        $name = 'Recipe Groups';

        return (new CategoryDirectory())->index($name, $search);
    }
}

if ( ! function_exists('transaction_types')) {
    /**
     * @throws Throwable
     */
    function transaction_types(string $search = ''): Collection|Category
    {
        $name = 'Transaction Types';

        return (new CategoryDirectory())->index($name, $search);
    }
}
