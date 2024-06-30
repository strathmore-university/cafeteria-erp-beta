<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToArticle;
use App\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use BelongsToArticle, BelongsToTeam;

    protected $guarded = [];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    protected static function booted(): void
    {
        static::creating(function (StockMovement $movement): void {
            $value = abs($movement->units) * $movement->weighted_cost;
            $movement->stock_value = $value;
        });
    }
}
