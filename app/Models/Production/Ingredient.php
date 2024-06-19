<?php

namespace App\Models\Production;

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

        public function requestedIngredient(): HasOne
        {
            return $this->hasOne(RequestedIngredient::class);
        }

    public function requiredQuantity(int $portions): int
    {
        $recipe = $this->recipe;
        $quantity = $this->getAttribute('quantity');
        $calculated = $quantity * $portions / $recipe->yield;

        return match ($recipe->yield === $portions) {
            false => (int) ceil($calculated),
            true => $quantity,
        };
    }
}
