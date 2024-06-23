<?php

namespace App\Models\Production;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasStores;
use App\Models\Inventory\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

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

    public function daysMenu(): Menu
    {
        return $this->getMenuByDay(today()->dayName);
    }

    public function menus(): MorphMany
    {
        return $this->morphMany(Menu::class, 'owner');
    }

    public function getMenuByDay(string $day): Menu
    {
        return Menu::whereOwnerId($this->id)
            ->whereOwnerType(Restaurant::class)
            ->whereActiveDay($day)
            ->first();
    }

    public function getTomorrowsMenu(): Menu
    {
        $day = today()->addDay()->dayName;

        return $this->getMenuByDay($day);
    }

    protected static function booted(): void
    {
        static::created(function (Restaurant $restaurant): void {
            $name = $restaurant->getAttribute('name');
            $item = collect();

            $item->push([
                'category_id' => store_types('Main Store')->id,
                'name' => $name . ' store',
                'is_default' => true,
            ]);

            $item->push([
                'category_id' => store_types('Sales Point')->id,
                'name' => $name . 'sales store',
                'can_ship_stock' => false,
            ]);

            $restaurant->stores()->createMany($item->toArray());

            $days = collect(Carbon::getDays());
            $days->each(function ($day) use ($restaurant, $name): void {
                $restaurant->menus()->create([
                    'name' => $name . ' ' . $day . ' menu',
                    'active_day' => $day,
                ]);
            });
        });
    }

    private function fetchStore(
        string $key,
        bool $value,
        ?array $select = null
    ): Store {
        $select = $select ?? ['id', 'team_id', 'is_hidden', 'can_ship_stock'];

        return Store::whereOwnerType($this->getMorphClass())
            ->where($key, '=', $value)
            ->whereOwnerId($this->getKey())
            ->select($select)
            ->first();
    }
}
