<?php

namespace App\Models\Production;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCategory;
use App\Concerns\HasOwner;
use App\Concerns\HasStores;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    use BelongsToTeam, HasCategory, HasOwner, HasStores;

    protected $guarded = [];

    public function shifts(): HasMany
    {
        return $this->hasMany(CookingShift::class);
    }

    public function fetchShift(): CookingShift
    {
        $shift = CookingShift::whereStationId($this->id)
            ->whereDate('created_at', today())
            ->first();

        return match (filled($shift)) {
            default => $this->shifts()->create(),
            true => $shift,
        };
    }

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
}
