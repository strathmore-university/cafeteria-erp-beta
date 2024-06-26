<?php

use App\Filament\Clusters\Inventory\Resources\ArticleResource;
use App\Filament\Clusters\Inventory\Resources\StockTakeResource;
use App\Filament\Clusters\Inventory\Resources\StockTransferResource;
use App\Filament\Clusters\Inventory\Resources\StoreResource;
use App\Filament\Clusters\Procurement\Resources\CreditNoteResource;
use App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource;
use App\Filament\Clusters\Production\Resources\CookingShiftResource;
use App\Filament\Clusters\Production\Resources\FoodOrderResource;
use App\Filament\Clusters\Production\Resources\MenuItemResource;
use App\Filament\Clusters\Production\Resources\MenuResource;
use App\Filament\Clusters\Production\Resources\ProductDispatchResource;
use App\Filament\Clusters\Production\Resources\RecipeResource;
use App\Filament\Clusters\Production\Resources\RestaurantResource;
use App\Models\Core\Review;
use App\Models\Core\Team;
use App\Models\Core\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

if ( ! function_exists('auth_team')) {
    function auth_team(): Team
    {
        return match (auth()->check()) {
            true => Team::find(team_id()),
            false => system_team(),
        };
    }
}

if ( ! function_exists('system_team')) {
    function system_team(): Team
    {
        return Cache::rememberForever('system_team', function () {
            return Team::where('is_default', '=', true)
                ->first();
        });
    }
}

if ( ! function_exists('team_id')) {
    function team_id(): int
    {
        return auth()->user()->team_id ?? system_team()->id;
    }
}

if ( ! function_exists('system_user')) {
    function system_user(): User
    {
        return Cache::rememberForever(
            'system_user',
            fn () => User::whereUsername('system_user')->first()
        );
    }
}

if ( ! function_exists('primary_units')) {
    function primary_units(): Collection
    {
        return Unit::isReference()->select(['id', 'name'])->get();
    }
}

if ( ! function_exists('unit_descendants')) {
    function unit_descendants(string $name): Collection
    {
        return Unit::with('descendants')
            ->where('is_reference', '=', true)
            ->where('name', '=', $name)
            ->select(['id', '_lft', '_rgt', 'parent_id'])
            ->first()
            ->descendants;
    }
}

if ( ! function_exists('get_unit_by_name')) {
    function get_unit_by_name(string $name): Unit
    {
        return Unit::where('is_reference', '=', false)
            ->where('name', '=', $name)
            ->first();
    }
}

if ( ! function_exists('reviewable_types')) {
    function reviewable_types(): array
    {
        return [
            'App\Models\Procurement\PurchaseOrder' => 'PurchaseOrder',
        ];
    }
}

if ( ! function_exists('get_record_url')) {
    function get_record_url(Model $model, array $attributes = []): string
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
            'ProductDispatch' => ProductDispatchResource::class,
            'PurchaseOrder' => PurchaseOrderResource::class,
            'StockTransfer' => StockTransferResource::class,
            'CookingShift' => CookingShiftResource::class,
            'CreditNote' => CreditNoteResource::class,
            'Restaurant' => RestaurantResource::class,
            'FoodOrder' => FoodOrderResource::class,
            'StockTake' => StockTakeResource::class,
            'MenuItem' => MenuItemResource::class,
            'Article' => ArticleResource::class,
            'Recipe' => RecipeResource::class,
            'Store' => StoreResource::class,
            'Menu' => MenuResource::class,
            default => ''
        };

        $attributes = array_merge(['record' => $id], $attributes);

        return $resource::getUrl('view', $attributes);
    }
}
