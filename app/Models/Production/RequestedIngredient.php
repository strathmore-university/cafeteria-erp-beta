<?php

namespace App\Models\Production;

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Throwable;

class RequestedIngredient extends Model
{
    protected $guarded = [];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    //    public function recipe(): BelongsTo
    //    {
    //        return $this->belongsTo(Recipe::class);
    //    }

    public function foodOrderRecipe(): BelongsTo
    {
        return $this->belongsTo(FoodOrderRecipe::class);
    }

    public function batches(): MorphMany
    {
        return $this->morphMany(Batch::class, 'owner');
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function dispatchedIngredients(): HasMany
    {
        return $this->hasMany(DispatchedIngredient::class);
    }

    public function foodOrder(): BelongsTo
    {
        return $this->belongsTo(FoodOrder::class);
    }

    public function isFulfilled(): bool
    {
        return $this->remaining_quantity === 0;
    }

    public function isPendingFulfilment(): bool
    {
        return ! $this->isFulfilled();
    }

    /**
     * @throws Throwable
     */
    public function createDispatchIngredient(
        Article $article,
        int $units
    ): void {
        // todo: prevent dispatching surplus
        // todo: add transaction & try catch

        $dispatchedIngredient = $this->dispatchedIngredients()->create([
            'food_order_recipe_id' => $this->food_order_recipe_id,
            'unit_id' => $article->getAttribute('unit_id'),
            'food_order_id' => $this->food_order_id,
            'store_id' => $this->store_id,
            'dispatched_by' => auth_id(),
            'article_id' => $article->id,
            'status' => 'draft',
            'units' => $units,
        ]);

        $this->updateRemainingQuantity(
            $article,
            $units,
            $dispatchedIngredient
        );
    }

    public function utilize(FoodOrderRecipe $foodOrderRecipe): void
    {
        $ingredient = $this->ingredient;
        $portions = $foodOrderRecipe->expected_portions;
        $quantityToConsume = $ingredient->requiredQuantity($portions);

        $remaining = $quantityToConsume;

        //        dump('Utilizing ' . $quantityToConsume . ' ' . $ingredient->unit->code . ' of ' . $ingredient->article->name . '.');
        //        dump($remaining . ' remaining');

        $this->batches->each(function (Batch $batch) use ($remaining): void {
            if ($remaining <= 0) {
                return;
            }

            $article = $batch->article;
            $id = $article->unit_measurement_id;
            $quantity = $this->unit->convert($id, $remaining);
            dump('reducing ' . $quantity . ' ' . $article->unit->code . ' of ' . $article->name . '.');
            //            $units = $quantity / $article->unit_quantity;

            //            dump('reducing ' . $units . 'units');
            $batch->remaining_units -= $quantity;
            $batch->utilised_at = now();
            $batch->update();

            // todo: instead of reducing

            $remaining -= $quantity;
            //            $this->dispatchIngredient($batch->article, $quantity);
        });

        //        $article->
        //        $this->dispatchIngredient($article, $quantity);
        // recalculate the quantity of ingredients to use
        //        $quantity =

        // reduce the quantity of ingredients used
        // update the status of the batch
    }

    /**
     * @throws Throwable
     */
    protected function getCapacityAtStationAttribute(): int
    {
        $reference = $this->article;
        $store = $this->store;

        // first get list of descendants. check if that store has batches of those units
        // todo: do this here! check for batches that don't have owners
        $capacity = article_capacity($reference, $store);

        return 10 ?? $capacity;
    }

    /**
     * @throws Throwable
     */
    private function updateRemainingQuantity(
        Article $article,
        int $units,
        DispatchedIngredient $dispatchedIngredient
    ): void {
        $capacity = $units * $article->unit_capacity;
        $from = $dispatchedIngredient->unit;
        $to = $this->unit;

        $units = quantity_converter($from, $to, $capacity);
        $this->remaining_quantity -= ceil($units);
        $this->dispatched_units += ceil($units);
        $this->update();
    }
}
