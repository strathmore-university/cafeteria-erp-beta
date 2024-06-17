<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
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

        $message = 'StockMovement is immutable';
        static::updating(fn () => throw_if(true, $message));

        static::saving(
            fn (Model $model) => throw_if($model->exists, $message)
        );
    }
}
