<?php

namespace App\Models\Production;

use App\Concerns\BelongsToArticle;
use App\Models\Core\Unit;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DispatchedIngredient extends Model
{
    use BelongsToArticle;

    protected $guarded = [];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function batches(): MorphMany
    {
        return $this->morphMany(Batch::class, 'owner');
    }

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function foodOrder(): BelongsTo
    {
        return $this->belongsTo(FoodOrder::class);
    }

    public function requestedIngredient(): BelongsTo
    {
        return $this->belongsTo(RequestedIngredient::class);
    }

    protected static function booted(): void
    {
        parent::creating(function (DispatchedIngredient $ingredient): void {
            if ($ingredient->getAttribute('status') === 'draft') {
                $ingredient->current_units = $ingredient->initial_units;
            }
        });
    }
}
