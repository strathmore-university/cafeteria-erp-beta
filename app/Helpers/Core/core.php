<?php

use App\Filament\Clusters\Inventory\Resources\StockTakeResource;
use App\Filament\Clusters\Inventory\Resources\StoreResource;
use App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource;
use App\Models\Core\Review;
use App\Models\Core\Team;
use App\Models\Core\Unit;
use App\Models\User;
use App\Support\Core\QuantityConverter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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

if (! function_exists('reviewable_types')) {
    function reviewable_types(): array
    {
        return [
            'App\Models\Procurement\PurchaseOrder' => 'PurchaseOrder',
        ];
    }
}

if (! function_exists('get_record_url')) {
    function get_record_url(Model $model): string
    {
        $check = $model instanceof Review;
        $class = match ($check) {
            true => $model->getAttribute('reviewable_type'),
            default => $model
        };

        $id = match ($check) {
            true => $model->getAttribute('reviewable_id'),
            default => $model->getKey()
        };

        $resource = match (class_basename($class)) {
            'GoodsReceivedNote' => GoodsReceivedNoteResource::class,
            'PurchaseOrder' => PurchaseOrderResource::class,
            'StockTake' => StockTakeResource::class,
            'Store' => StoreResource::class,
            default => ''
        };

        return $resource::getUrl('view', ['record' => $id]);
    }
}
