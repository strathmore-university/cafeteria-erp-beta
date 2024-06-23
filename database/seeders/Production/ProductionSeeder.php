<?php

namespace Database\Seeders\Production;

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Production\FoodOrder;
use App\Models\Production\Menu;
use App\Models\Production\Recipe;
use App\Models\Production\Restaurant;
use App\Models\Production\Station;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
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
        $restaurant = Restaurant::create([
            'description' => 'SU STC cafeteria',
            'name' => 'Su Cafeteria',
        ]);

        $names = [
            'Saucier Station', 'Entre metier Station', 'Fry Station', 'Pâtissier Station',
        ];

        $team = system_team();

        collect($names)->each(function ($name) use ($team): void {
            Station::create([
                'description' => $name . ' description',
                'owner_type' => $team->getMorphClass(),
                'owner_id' => $team->id,
                'team_id' => $team->id,
                'is_active' => true,
                'name' => $name,
            ]);
        });

        $store = system_team()->defaultStore();
        $portion = Unit::where('name', '=', 'Portion')->first();

        $recipes = [
            ['name' => 'Spaghetti Bolognese', 'description' => 'Spaghetti Bolognese description', 'yield' => 8],
            ['name' => 'Vegetable Stir Fry', 'description' => 'Vegetable Stir Fry description', 'yield' => 20],
            ['name' => 'Beef Stroganoff', 'description' => 'Beef Stroganoff description', 'yield' => 12],
            ['name' => 'Chicken Curry', 'description' => 'Chicken Curry description', 'yield' => 15],
            ['name' => 'Lasagna', 'description' => 'Lasagna description', 'yield' => 10],
        ];

        collect($recipes)->each(function ($recipeData) use ($store, $portion): void {
            $article = Article::create([
                'is_profit_contributing' => true,
                'name' => $recipeData['name'],
                'unit_id' => $portion->id,
                'store_id' => $store->id,
                'is_product' => true,
            ]);

            $recipe = Recipe::create([
                'category_id' => recipe_groups()->random()->id,
                'description' => $recipeData['description'],
                'yield' => $recipeData['yield'],
                'name' => $recipeData['name'],
                'article_id' => $article->id,
                'surplus_tolerance' => fake()->numberBetween(1, 10),
                'wastage_tolerance' => fake()->numberBetween(1, 10),
            ]);

            $ingredients = Article::inRandomOrder()->whereIsIngredient(true)->where('is_reference', true)->get();
            $ingredients->take(random_int(5, 10))->each(function (Article $ingredient) use ($recipe): void {
                $recipe->ingredients()->create([
                    'unit_id' => $ingredient->getAttribute('unit_id'),
                    'article_id' => $ingredient->id,
                    'quantity' => random_int(2, 10),
                ]);
            });
        });

        Recipe::all()->each(function (Recipe $recipe): void {
            $byProducts = Article::whereIsProduct(true)->isDescendant()->inRandomOrder()->get();
            $byProducts->take(random_int(0, 2))->each(function (Article $byProduct) use ($recipe): void {
                $recipe->byProducts()->create([
                    'unit_id' => $byProduct->getAttribute('unit_id'),
                    'quantity' => random_int(2, 10),
                    'article_id' => $byProduct->id,
                ]);
            });
        });

        $articles = Article::whereIsProduct(true)->isDescendant()->get();

        $restaurant->menus->each(function (Menu $menu) use ($articles): void {
            $articles->each(function (Article $article) use ($menu): void {
                $menu->items()->create([
                    'portions_to_prepare' => round(random_int(50, 200), -1),
                    'selling_price' => round(random_int(50, 1000), -2),
                    'station_id' => Station::all()->random()->id,
                    'name' => $article->getAttribute('name'),
                    'code' => random_int(100, 999),
                    'article_id' => $article->id,
                ]);
            });
        });

        Artisan::call('schedule:test --name=auto:review-cooking-shifts');

        $foodOrder = FoodOrder::whereHas('recipe.byProducts')->first();

//        $foodOrder = $foodOrders->first();
//        $foodOrders->each(function (FoodOrder $foodOrder) {
            $foodOrder->requestIngredients();
            $foodOrder->populateDispatch();
            $foodOrder->executeIngredientDispatch();
            $foodOrder->initiate();
//        });
    }
}
