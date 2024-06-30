<?php

namespace App\Models\Production;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Menu extends Model
{
    use BelongsToTeam, HasOwner, SoftDeletes;

    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function itemsInStock(): Collection
    {
        return $this->items->filter(fn ($item) => $item->isInStock());
    }
}
