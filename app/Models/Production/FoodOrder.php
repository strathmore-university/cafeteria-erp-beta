<?php

namespace App\Models\Production;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasReviews;
use App\Concerns\HasStatusTransitions;
use App\Models\Inventory\Batch;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Throwable;

class FoodOrder extends Model
{
    use BelongsToTeam, HasReviews, SoftDeletes;
    use HasStatusTransitions;

    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(FoodOrderRecipe::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function dispatchedIngredients(): HasMany
    {
        return $this->hasMany(DispatchedIngredient::class);
    }

    public function requestedIngredients(): HasMany
    {
        return $this->hasMany(RequestedIngredient::class);
    }

    public function canRequestIngredients(): bool
    {
        return RequestedIngredient::whereFoodOrderId($this->id)->doesntExist();
    }

    public function canPopulateDispatch(): bool
    {
        $three = DispatchedIngredient::whereFoodOrderId($this->id)->doesntExist();
        $two = blank($this->ingredients_dispatched_at);
        $one = ! $this->canRequestIngredients();

        return and_check(and_check($one, $two), $three);
    }

    public function canExecuteDispatch(): bool
    {
        $three = DispatchedIngredient::whereFoodOrderId($this->id)->exists();
        $two = blank($this->ingredients_dispatched_at);
        $one = ! $this->canRequestIngredients();

        return and_check(and_check($one, $two), $three);
    }

    /**
     * @throws Throwable
     */
    public function requestIngredients(): void
    {
        try {
            DB::transaction(function (): void {
                $store = $this->station->stores->first();

                $this->items->each(function (FoodOrderRecipe $foodPrepItem) use ($store): void {
                    $ingredients = $foodPrepItem->recipe->ingredients;
                    $ingredients->each(function ($ingredient) use ($store, $foodPrepItem): void {
                        $portions = $foodPrepItem->expected_portions;
                        $requiredQuantity = $ingredient->requiredQuantity($portions);

                        $foodPrepItem->requestedIngredients()->create([
                            'food_order_recipe_id' => $foodPrepItem->id,
                            'remaining_quantity' => $requiredQuantity,
                            'required_quantity' => $requiredQuantity,
                            'article_id' => $ingredient->article_id,
                            'ingredient_id' => $ingredient->id,
                            'unit_id' => $ingredient->unit_id,
                            'food_order_id' => $this->id,
                            'store_id' => $store->id,
                        ]);
                    });
                });

                success();
                redirect(get_record_url($this));
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }

    public function executeIngredientDispatch(): void
    {
        try {
            $this->items->each(function (FoodOrderRecipe $foodOrderRecipe): void {
                $items = DispatchedIngredient::whereFoodOrderRecipeId($foodOrderRecipe->id)->get();

                $items->each(function (DispatchedIngredient $dispatchedIngredient): void {
                    $dispatchedIngredient->dispatch();
                });

                $foodOrderRecipe->ingredients_dispatched_at = now();
                $foodOrderRecipe->status = 'pending preparation';
                $foodOrderRecipe->update();
            });

            // fetch the dispatched items
            $items = DispatchedIngredient::whereFoodOrderId($this->id)->get();

            $items->each(function (DispatchedIngredient $dispatchedIngredient): void {
                $dispatchedIngredient->dispatch();
            });

            $this->ingredients_dispatched_at = now();
            $this->status = 'pending preparation';
            $this->update();

            success();
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }

    /**
     * @throws Throwable
     */
    public function initiate(): void
    {
        $key = 'food_preparation_id';
        $ids = RequestedIngredient::where($key, $this->id)
            ->select('id')
            ->get();

        Batch::where('owner_type', RequestedIngredient::class)
            ->whereIn('owner_id', $ids->pluck('id')->toArray())
            ->update(['locked_at' => now()]);

        $this->status = 'preparation started';
        $this->preparation_initiated = true;
        $this->update();
    }

    public function complete(): void
    {
        $this->items->each(
            /**
             * @throws Throwable
             * @throws RandomException
             */
            fn (FoodOrderRecipe $item) => $item->prepare()
        );

        $this->status = 'pending dispatch';
        $this->update();
    }

    //    public function dispatchItems(): HasMany
    //    {
    //        return $this->hasMany(ProductDispatchItem::class);
    //    }

    /**
     * @throws Throwable
     */
    public function populateDispatch(): void
    {
        //todo:
        dd();

        $restaurant = $this->restaurant;

        $this->items->each(function (FoodOrderRecipe $item) use ($restaurant): void {
            $article = $item->recipe->product;

            // todo: add flexibility to move / change restaurant or implement inter restaurant exchange..
            $item->dispatchItem()->create([
                'dispatched_quantity' => $item->produced_portions,
                'food_preparation_recipe_id' => $item->id,
                'dispatched_by' => $this->prepared_by,
                'restaurant_id' => $restaurant->id,
                'food_preparation_id' => $this->id,
                'station_id' => $this->station_id,
                'article_id' => $article->id,
                'dispatched_at' => now(),
            ]);
        });

        $this->status = 'dispatched';
        $this->update();
    }

    public function receive(): void
    {
        $restaurant = $this->restaurant;
        $to = $restaurant->defaultStore();
        $from = $this->station->stores->first();

        $dispatchedItems = $this->dispatchItems;

        $dispatchedItems->each(
            /**
             * @throws Throwable
             */
            function (ProductDispatchItem $item) use ($from, $to): void {
                $item->received_quantity = $item->dispatched_quantity;
                $item->received_by = $this->prepared_by;
                $item->received_at = now();
                $item->update();

                $article = $item->product;
                inventory()->stock()->move($article, $from, $to, $item->received_quantity);
            }
        );

        $this->status = 'Received';
        $this->update();
    }

    public function hasAdequateIngredients(): bool
    {
        // todo: allow dispatching less stock?

        return $this->items->every(
            fn (FoodOrderRecipe $item) => $item->hasAdequateIngredients()
        );
    }

    public function canBeSubmittedForReview(): bool
    {
        return false;
    }

    public function requestReview(): void
    {
        // TODO: Implement requestReview() method.
    }

    public function approvalAction(): void
    {
        // TODO: Implement approvalAction() method.
    }

    public function returnAction(): void
    {
        // TODO: Implement returnAction() method.
    }

    public function rejectedAction(): void
    {
        // TODO: Implement rejectedAction() method.
    }

    public function statusTransitions(): array
    {
        return [
            //            'draft'
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (FoodOrder $foodOrder): void {
            $id = get_next_id($foodOrder);
            $code = generate_code('FO-', $id);

            $foodOrder->setAttribute('code', $code);
            //            $foodOrder->setAttribute('status', 'draft'); // todo:
        });
    }

    protected function casts(): array
    {
        return [
            'ingredients_dispatched_at' => 'datetime',
        ];
    }
}
