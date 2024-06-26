<?php

namespace App\Actions\Production;

use App\Models\Inventory\Store;
use App\Models\Production\FoodOrder;
use App\Models\Production\Ingredient;
use App\Models\Production\RequestedIngredient;
use App\Models\Production\Station;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class RequestFoodOrderIngredients
{
    private Collection $ingredients;
    private FoodOrder $foodOrder;
    private Collection $items;
    private Store $store;

    private function setUp(FoodOrder $foodOrder): void
    {
        $this->foodOrder = $foodOrder;
        $this->items = collect();

        $this->store = Store::whereOwnerType(Station::class)
            ->whereOwnerId($this->foodOrder->station_id)
            ->select('id')
            ->first();

        $this->ingredients = Ingredient::with('recipe:id,yield')
            ->whereRecipeId($foodOrder->recipe_id)
            ->select(['id', 'article_id', 'unit_id', 'quantity', 'recipe_id'])
            ->get();
    }

    public function execute(FoodOrder $foodOrder): void
    {
        $this->setUp($foodOrder);

        try {
            DB::transaction(function (): void {
                $this->ingredients->each(fn ($item) => $this->request($item));

                RequestedIngredient::insert($this->items->toArray());

                success();
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }

    private function request(Ingredient $ingredient): void
    {
        $portions = $this->foodOrder->expected_portions;
        $requiredQuantity = $ingredient->requiredQuantity($portions);

        $item = [
            'remaining_quantity' => $requiredQuantity,
            'required_quantity' => $requiredQuantity,
            'food_order_id' => $this->foodOrder->id,
            'article_id' => $ingredient->article_id,
            'ingredient_id' => $ingredient->id,
            'unit_id' => $ingredient->unit_id,
            'store_id' => $this->store->id,
        ];

        $this->items->push($item);
    }
}
