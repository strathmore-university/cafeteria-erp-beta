<?php

namespace App\Models\Production;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCategory;
use App\Concerns\HasStores;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use BelongsToTeam, HasCategory, HasStores;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::created(function (Station $station): void {
            $name = $station->getAttribute('name');
            $id = store_types('Main Store')->id ?? null;
            $station->stores()->create([
                'name' => $name . ' store',
                'category_id' => $id,
                'is_default' => true,
            ]);
        });
    }

    //    public function menuItems(): HasMany
    //    {
    //        return $this->hasMany(MenuItem::class);
    //    }
}
