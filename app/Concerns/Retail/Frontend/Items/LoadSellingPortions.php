<?php

namespace App\Concerns\Retail\Frontend\Items;

use App\Models\Production\SellingPortion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

trait LoadSellingPortions
{
    public function loadPortions(): void
    {
        //        todo: revisit
        $restaurantId = 1;
        $menuId = 1;

        //                Cache::forget($key);
        $key = 'menu_' . $menuId . 'sellingPortions';
        $expiresAt = now()->addHours(12);

        $this->allSellingPortions = Cache::remember($key, $expiresAt, function () {
            return SellingPortion::with('menuItem')
                ->whereHas('menuItem', function (Builder $builder): void {
                    $builder->where('menu_id', '=', 1);
                })->get();
        });

        $this->sellingPortions = $this->allSellingPortions;
    }
}
