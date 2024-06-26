<?php

namespace App\Models\Production;

use App\Concerns\BelongsToArticle;
use App\Models\Core\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use BelongsToArticle, SoftDeletes;

    protected $guarded = [];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function requiredQuantity(int $portions): int
    {
        $quantity = $this->getAttribute('quantity');
        $calculated = $quantity * $portions / $this->recipe->yield;

        return match ($this->recipe->yield === $portions) {
            false => (int) ceil($calculated),
            true => $quantity,
        };
    }
}
