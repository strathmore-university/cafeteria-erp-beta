<?php

namespace App\Models\Production;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasStores;
use App\Models\Inventory\Store;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use BelongsToTeam, HasStores;

    protected $guarded = [];

    public function salesStore(?array $select = null): Store
    {
        return $this->fetchStore(
            'can_ship_stock',
            false,
            $select
        );
    }

    public function defaultStore(?array $select = null): Store
    {
        return $this->fetchStore(
            'is_default',
            true,
            $select
        );
    }

    protected static function booted(): void
    {
        static::created(function (Restaurant $restaurant): void {
            $name = $restaurant->getAttribute('name');
            $item = collect();

            $id = store_types('Main Store')->id ?? null;
            $item->push([
                'name' => $name . ' store',
                'category_id' => $id,
                'is_default' => true,
            ]);

            $id = store_types('Sales Point')->id ?? null;
            $item->push([
                'name' => $name . 'sales store',
                'can_ship_stock' => false,
                'category_id' => $id,
            ]);

            $restaurant->stores()->createMany($item->toArray());

            //            collect(Carbon::getDays())->each(function ($day) use ($restaurant) {
            //                $restaurant->menus()->create([
            //                    'name' => $restaurant->name. ' ' .  $day .' menu',
            //                    'team_id' => $restaurant->team_id,
            //                    'active_day' => $day,
            //                ]);
            //            });
        });
    }

    //    public function daysMenu(): Menu
    //    {
    //        return Menu::where('restaurant_id', $this->id)
    //            ->where('active_day', '=', today()->dayName)
    //            ->first();
    //    }
    //
    //    public function menus(): HasMany
    //    {
    //        return $this->hasMany(Menu::class);
    //    }

    //    public function getMenuByDay(string $day): Menu
    //    {
    //        return Menu::where('restaurant_id', $this->id)
    //            ->where('active_day', '=', $day)
    //            ->first();
    //    }

    //    public function getTomorrowsMenu(): Menu
    //    {
    //        $day = today()->addDay()->dayName;
    //
    //        return Menu::where('restaurant_id', $this->id)
    //            ->where('active_day', '=', $day)
    //            ->first();
    //    }

    private function fetchStore(
        string $key,
        bool $value,
        ?array $select = null
    ): Store {
        $select = $select ?? ['id', 'team_id', 'is_hidden', 'can_ship_stock'];

        return Store::whereStoreableType($this->getMorphClass())
            ->whereStoreableId($this->getKey())
            ->where($key, '=', $value)
            ->select($select)
            ->first();
    }
}
