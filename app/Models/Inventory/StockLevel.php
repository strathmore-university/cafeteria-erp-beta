<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLevel extends Model
{
    use BelongsToTeam;

    protected $guarded = [];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeInventoryStock(Builder $query): Builder
    {
        return $query->where('is_sold_stock', '=', false);
    }

    public function scopeSalesStock(Builder $query): Builder
    {
        return $query->where('is_sold_stock', '=', true);
    }
}
