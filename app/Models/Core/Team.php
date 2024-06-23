<?php

namespace App\Models\Core;

use App\Concerns\HasStores;
use App\Models\Production\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasStores, SoftDeletes;

    protected $guarded = [];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user');
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    protected static function booted(): void
    {
        static::created(function (Team $team): void {
            $name = $team->getAttribute('name');

            $category = store_types('Procurement Store');
            $team->stores()->create([
                'name' => $name . ' ' . $category->getAttribute('name'),
                'description' => 'Procurement Store',
                'category_id' => $category->id,
                'owner_type' => Team::class,
                'owner_id' => $team->id,
            ]);

            $category = store_types('Main Store');
            $team->stores()->create([
                'name' => $name . ' ' . $category->getAttribute('name'),
                'description' => 'Main Store',
                'category_id' => $category->id,
                'owner_type' => Team::class,
                'owner_id' => $team->id,
                'is_default' => true,
            ]);
        });
    }
}
