<?php

use App\Models\Core\Team;
use App\Models\Core\Unit;
use App\Models\User;
use App\Support\Core\QuantityConverter;
use Illuminate\Database\Eloquent\Collection;

if (! function_exists('quantity_converter')) {
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

if (! function_exists('system_team')) {
    function system_team(): Team
    {
        return Team::where('is_default', '=', true)->first();
    }
}

if (! function_exists('system_user')) {
    function system_user(): User
    {
        // todo: update system user
        return User::where('email', '=', 'tony@gmail.com')->first();
    }
}

if (! function_exists('primary_units')) {
    function primary_units(): Collection
    {
        return Unit::isReference()->select(['id', 'name'])->get();
    }
}

if (! function_exists('unit_descendants')) {
    function unit_descendants(int $id): Collection
    {
        return Unit::with('descendants')
            ->select(['id', '_lft', '_rgt', 'parent_id'])
            ->find($id)
            ->descendants;
    }
}
