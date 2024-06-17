<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCategory;
use App\Concerns\HasOwner;
use App\Concerns\HasStock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use BelongsToTeam, HasCategory, SoftDeletes;
    use HasOwner, HasStock;

    protected $guarded = [];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public static function booted(): void
    {
        static::creating(function (Store $store): void {
            $one = filled($store->owner_id);
            $two = filled($store->owner_type);

            if (and_check($two, $one)) {
                return;
            }

            $team = system_team();
            $store->owner_type = $team->getMorphClass();
            $store->owner_id = $team->id;
        });
    }
}
