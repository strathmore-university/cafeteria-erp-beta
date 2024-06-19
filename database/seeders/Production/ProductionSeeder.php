<?php

namespace Database\Seeders\Production;

use App\Models\Inventory\Article;
use App\Models\Production\FoodOrder;
use App\Models\Production\Recipe;
use App\Models\Production\Restaurant;
use App\Models\Production\Station;
use Illuminate\Database\Seeder;
use Random\RandomException;
use Throwable;

class ProductionSeeder extends Seeder
{
    /**
     * @throws Throwable
     * @throws RandomException
     */
    public function run(): void
    {
        Restaurant::create([
            'name' => 'Su Cafeteria',
            'team_id' => team_id(),
            'description' => 'Restaurant 1 description',
            'is_active' => true,
        ]);

        $names = [
            'Saucier Station', 'Entre metier Station', 'Fry Station', 'Pâtissier Station',
        ];

        collect($names)->each(function ($name): void {
            Station::create([
                'name' => $name,
                'team_id' => team_id(),
                'description' => $name . ' description',
                'is_active' => true,
            ]);
        });

        $products = Article::isProduct()->get();
        $ingredients = Article::whereIsIngredient(true)->isReference()->get();

        $products->each(/**
         * @throws RandomException
         * @throws Throwable
         */ function ($product) use ($ingredients): void {
            $recipe = $product->recipe()->create(
                Recipe::factory()->make([
                    'name' => $product->getAttribute('name'),
                    'category_id' => recipe_groups()->random()->id,
                    'yield' => random_int(1, 10),
                ])->toArray()
            );

            $ingredients->each(function ($ingredient) use ($recipe): void {
                $recipe->ingredients()->create([
                    'unit_id' => $ingredient->getAttribute('unit_id'),
                    'article_id' => $ingredient->id,
                    'quantity' => random_int(1, 10),
                ]);
            });
        });

        $restaurant = Restaurant::first();
        $station = Station::first();

        $foodOrder = FoodOrder::create([
            'description' => fake()->sentence(),
            'restaurant_id' => $restaurant->id,
            'status' => 'pending preparation',
            'station_id' => $station->id,
        ]);

        $recipe = Recipe::all();
        $foodOrder->items()->create([
            'expected_portions' => random_int(1, 10),
            'recipe_id' => $recipe->random()->id,
        ]);
    }
}
