<?php

namespace App\Models\Production;

use App\Actions\Inventory\ExecuteIngredientDispatch;
use App\Actions\Inventory\PopulateDispatch;
use App\Actions\Production\CompleteFoodOrder;
use App\Actions\Production\RequestFoodOrderIngredients;
use App\Concerns\BelongsToTeam;
use App\Concerns\HasOwner;
use App\Concerns\HasStatusTransitions;
use App\Filament\Clusters\Production\Resources\FoodOrderResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Throwable;

class FoodOrder extends Model
{
    use BelongsToTeam, HasOwner, SoftDeletes;
    use HasStatusTransitions;

    protected $guarded = [];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CookingShift::class);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function requestedIngredients(): HasMany
    {
        return $this->hasMany(RequestedIngredient::class);
    }

    public function byProducts(): HasMany
    {
        return $this->hasMany(FoodOrderByProducts::class);
    }

    public function dispatchedIngredients(): HasMany
    {
        return $this->hasMany(DispatchedIngredient::class);
    }

    public function requestIngredients(): void
    {
        (new RequestFoodOrderIngredients())->execute($this);
    }

    public function canRequestIngredients(): bool
    {
        $one = blank($this->ingredients_dispatched_at);
        $two = $this->requestedIngredients()->doesntExist();

        return and_check($one, $two);
    }

    public function canPopulateDispatch(): bool
    {
        $one = $this->dispatchedIngredients()->doesntExist();
        $two = blank($this->ingredients_dispatched_at);
        $check = and_check($one, $two);

        return and_check($check, !$this->canRequestIngredients());
    }

    public function canExecuteDispatch(): bool
    {
        $three = $this->dispatchedIngredients()->exists();
        $two = blank($this->ingredients_dispatched_at);
        $one = !$this->canRequestIngredients();

        return and_check(and_check($one, $two), $three);
    }

    public function populateDispatch(): void
    {
        (new PopulateDispatch)->execute($this);
    }

    public function executeIngredientDispatch(): void
    {
        (new ExecuteIngredientDispatch())->execute($this);
    }

    public function canBeInitiated(): bool
    {
        $status = $this->getAttribute('status');
        $one = filled($this->ingredients_dispatched_at);
        $two = $status === 'pending preparation';

        return and_check($two, $one);
    }

    public function initiate(): void
    {
        $this->setAttribute('status', 'started');
        $this->initiated_at = now();
        $this->update();

        success();
    }

    public function canRecordRemainingStock(): bool
    {
        $status = $this->getAttribute('status');
        $one = blank($this->prepared_at);
        $two = $status === 'started';
        $three = !$this->has_recorded_remaining_stock;
        $check = and_check($two, $one);

        return and_check($check, $three);
    }

    public function canRecordByProductsStock(): bool
    {
        $one = $this->has_recorded_remaining_stock;
        $two = !$this->has_recorded_by_products;

        return and_check($one, $two);
    }

    public function remainingStockUrl(): string
    {
        return FoodOrderResource::getUrl(
            'record-stock', ['record' => $this]
        );
    }

    public function recordByProductUrl(): string
    {
        return FoodOrderResource::getUrl(
            'record-by-products', ['record' => $this]
        );
    }

    public function canBeCompleted(): bool
    {
        $one = $this->has_recorded_remaining_stock;
        $two = $this->has_recorded_by_products;

        return and_check($two, $one);
    }

    public function populateByProducts(): string
    {
        $id = $this->getAttribute('recipe_id');
        $recipe = Recipe::select('id')->find($id);
        $products = RecipeByProduct::whereRecipeId($recipe->id)->get();

        if (blank($products)) {
            return get_record_url($this);
        }

        $items = collect();
        $products->each(function (RecipeByProduct $product) use ($items) {
            $data = $product->only([
                'article_id', 'unit_id', 'quantity'
            ]);

            $more = [
                'food_order_id' => $this->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $items->push(array_merge($data, $more));
        });

        FoodOrderByProducts::insert($items->toArray());

        return FoodOrderResource::getUrl('record-by-products', [
            'record' => $this
        ]);
    }

    public function complete(array $data): void
    {
        (new CompleteFoodOrder())->execute($this, $data);
    }

    // todo: lorem ipsum

    //    public function hasAdequateIngredients(): bool
    //    {
    //        $key = 'food_order_recipe_id';
    //
    //        $requested = RequestedIngredient::where($key, $this->id)
    //            ->select(['id', 'remaining_quantity'])
    //            ->get();
    //
    //        if ($requested->isEmpty()) {
    //            return false;
    //        }
    //
    //        $requested = $requested->where('remaining_quantity', '>', 0);
    //
    //        return $requested->isEmpty();
    //    }

    //    public function dispatchItem(): HasOne
    //    {
    //        return $this->hasOne(ProductDispatchItem::class);
    //    }

    //    /**
    //     * @throws RandomException
    //     * @throws Throwable
    //     */
    //    public function prepare(): void
    //    {
    //        $ingredients = $this->requestedIngredients;
    //        //        dump($ingredients->count());
    //
    //        $ingredients->each(function (RequestedIngredient $ingredient): void {
    //            $ingredient->utilize($this);
    //        });
    //
    //        //        todo: add production cost to the prep to batch ?
    //        $expected = $this->expected_portions;
    //        $this->produced_portions = random_int($expected / 2, $expected);
    //        $this->update();
    //
    //        $store = $this->foodPreparation->station->stores->first();
    //
    //        $article = $this->recipe->product;
    //
    //        $batch = Batch::create([
    //            'owner_id' => $this->id,
    //            'owner_type' => $this->getMorphClass(),
    //            'store_id' => $store->id,
    //            'article_id' => $article->id,
    //            'remaining_units' => $this->produced_portions,
    //            'initial_units' => $this->produced_portions,
    //            'narration' => 'Produced '.$this->produced_portions.' portions of '.$article->name.'.',
    //        ]);
    //
    //        inventory()
    //            ->stockLevel()
    //            ->update($store->team, $store, $article, $this->produced_portions);
    //
    //        dump('total stock for '.$article->name.$article->totalStock());
    //        // update the produced quantity / portions
    //        // create batch for product
    //    }


    //    public function complete(): void
    //    {
    //        $this->items->each(
    //            /**
    //             * @throws Throwable
    //             * @throws RandomException
    //             */
    //            fn (FoodOrder $item) => $item->prepare()
    //        );
    //
    //        $this->status = 'pending dispatch';
    //        $this->update();
    //    }

    /**
     * @throws Throwable
     */


    //    public function receive(): void
    //    {
    //        $restaurant = $this->restaurant;
    //        $to = $restaurant->defaultStore();
    //        $from = $this->station->stores->first();
    //
    //        $dispatchedItems = $this->dispatchItems;
    //
    //        $dispatchedItems->each(
    //            /**
    //             * @throws Throwable
    //             */
    //            function (ProductDispatchItem $item) use ($from, $to): void {
    //                $item->received_quantity = $item->dispatched_quantity;
    //                $item->received_by = $this->prepared_by;
    //                $item->received_at = now();
    //                $item->update();
    //
    //                $article = $item->product;
    //                inventory()->stock()->move($article, $from, $to, $item->received_quantity);
    //            }
    //        );
    //
    //        $this->status = 'Received';
    //        $this->update();
    //    }

    protected function casts(): array
    {
        return [
            'ingredients_dispatched_at' => 'datetime',
            'has_recorded_remaining_stock' => 'bool',
            'has_recorded_by_products' => 'bool',
            'requires_approval' => 'bool',
            'initiated_at' => 'datetime',
            'completed_at' => 'datetime',
            'prepared_at' => 'datetime',
            'is_flagged' => 'bool',
        ];
    }

    protected function statusTransitions(): array
    {
        return [
            'pending preparation' => 'initiated',
            'initiated' => 'pending dispatch',
            'pending dispatch' => 'completed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (FoodOrder $foodOrder): void {
            $id = get_next_id($foodOrder);
            $code = generate_code('FO-', $id);
            $foodOrder->setAttribute('code', $code);
        });
    }
}
