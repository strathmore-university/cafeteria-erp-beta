<?php

namespace App\Concerns;

use App\Models\Inventory\Store;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasStores
{
    //    public static function bootHasStores(): void
    //    {
    //        static::created(function (Model $model): void {
    //            $method = 'autoCreateStore';
    //
    //            match ($model->$method()) {
    //                true => self::attemptCreateStore($model),
    //                false => null,
    //            };
    //        });
    //    }

    public function stores(): MorphMany
    {
        return $this->morphMany(Store::class, 'owner');
    }

    public function defaultStore(): Store
    {
        return Store::where('storeable_type', $this->getMorphClass())
            ->where('is_default', '=', true)
            ->where('storeable_id', $this->getKey())
            ->first();
    }

    //    protected function autoCreateStore(): bool
    //    {
    //        return true;
    //    }
    //
    //    protected function limitsStoreCount(): bool
    //    {
    //        return false;
    //    }
    //
    //    protected function fetchAllowedStoreCount(): int
    //    {
    //        return 100000;
    //    }
    //
    //    private static function attemptCreateStore(Model $model): void
    //    {
    //        $method = 'limitsStoreCount';
    //
    //        match ($model->$method()) {
    //            true => self::checkStoreCount($model),
    //            false => self::createStore($model)
    //        };
    //    }
    //
    //    private static function createStore(Model $model): void
    //    {
    //        Store::create([
    //            'team_id' => $model->getAttribute('team_id') ?? $model->getKey(),
    //            'name' => Str::title(Str::lower($model->getAttribute('name'))),
    //            'storeable_type' => $model->getMorphClass(),
    //            'storeable_id' => $model->getKey(),
    //            'is_default' => true,
    //        ]);
    //    }
    //
    //    private static function checkStoreCount(Model $model): void
    //    {
    //        $storeCount = Store::whereStoreableId($model->getKey())
    //            ->whereStoreableType($model->getMorphClass())
    //            ->count();
    //
    //        $method = 'fetchAllowedStoreCount';
    //
    //        match ($storeCount === $model->$method()) {
    //            false => self::createStore($model),
    //            true => null,
    //        };
    //    }
}
