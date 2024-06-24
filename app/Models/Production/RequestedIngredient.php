<?php

namespace App\Models\Production;

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Throwable;

class RequestedIngredient extends Model
{
    protected $guarded = [];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
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

    /**
     * @throws Throwable
     */
    public function viableArticles(): array
    {
        $article = $this->article;
        $store = Store::select('id')->find(1); // todo: remove hardcoding
        $viable = $article->viableDispatchArticles($store);

        return $viable->pluck('name', 'id')->toArray();
    }

    public function isFulfilled(): bool
    {
        return $this->remaining_quantity < 1;
    }

    // todo: lorem
    //

    public function utilize(FoodOrder $foodOrderRecipe): void
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
    protected function getCapacityAtStationAttribute(): string
    {
        $id = $this->article_id;
        $reference = Article::with('descendants')->find($id);
        $store = Store::select('id')->find($this->store_id);

        $ids = $reference->descendants->pluck('id')->toArray();
        $batches = Batch::where('store_id', $store->id)
            ->where('owner_id', null)
            ->whereIn('article_id', $ids)
            ->exists();

        $id = $reference->getAttribute('unit_id');
        $unit = Unit::select('name')->find($id);

        $capacity = match ($batches) {
            true => article_capacity($reference, $store),
            false => 0
        };

        $name = $unit->getAttribute('name');
        $unitName = Str::plural($name, $capacity);

        return number_format($capacity) . ' ' . $unitName;
    }
}
