<?php

namespace App\Models\Production;

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Inventory\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchedIngredient extends Model
{
    protected $guarded = [];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function foodOrderRecipe(): BelongsTo
    {
        return $this->belongsTo(FoodOrderRecipe::class);
    }

    public function foodOrder(): BelongsTo
    {
        return $this->belongsTo(FoodOrder::class);
    }

    public function dispatch(): void
    {
        dd($this);

        //        $a = 1;

        // check stock levels?

        // get the article

        // find its batches

        // loop over batches
        // create new batch
        // create movement
        // update stock level

        // update the requested ingredient
        //        $this->requestedIngredient->update(['dispatched_at' => now()]);

        // update the status of the dispatched item

    }
}
