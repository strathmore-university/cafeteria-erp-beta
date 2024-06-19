<?php

namespace App\Models\Production;

use App\Models\Inventory\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Random\RandomException;
use Throwable;

class FoodOrderRecipe extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function foodOrder(): BelongsTo
    {
        return $this->belongsTo(FoodOrder::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function hasAdequateIngredients(): bool
    {
        $key = 'food_order_recipe_id';

        $requested = RequestedIngredient::where($key, $this->id)
            ->select(['id', 'remaining_quantity'])
            ->get();

        if ($requested->isEmpty()) {
            return false;
        }

        $requested = $requested->where('remaining_quantity', '>', 0);

        return $requested->isEmpty();
    }

    public function requestedIngredients(): HasMany
    {
        return $this->hasMany(RequestedIngredient::class);
    }

    public function dispatchItem(): HasOne
    {
        return $this->hasOne(ProductDispatchItem::class);
    }

    /**
     * @throws RandomException
     * @throws Throwable
     */
    public function prepare(): void
    {
        $ingredients = $this->requestedIngredients;
//        dump($ingredients->count());

        $ingredients->each(function (RequestedIngredient $ingredient) {
            $ingredient->utilize($this);
        });

//        todo: add production cost to the prep to batch ?
        $expected = $this->expected_portions;
        $this->produced_portions = random_int($expected / 2, $expected);
        $this->update();

        $store = $this->foodPreparation->station->stores->first();

        $article = $this->recipe->product;

        $batch = Batch::create([
            'owner_id' => $this->id,
            'owner_type' => $this->getMorphClass(),
            'store_id' => $store->id,
            'article_id' => $article->id,
            'remaining_units' => $this->produced_portions,
            'initial_units' => $this->produced_portions,
            'narration' => 'Produced ' . $this->produced_portions . ' portions of ' . $article->name . '.',
        ]);

        inventory()
            ->stockLevel()
            ->update($store->team, $store, $article, $this->produced_portions);

        dump('total stock for ' . $article->name . $article->totalStock());
        // update the produced quantity / portions
        // create batch for product
    }
}
