<?php

namespace Database\Seeders\Inventory;

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use Illuminate\Database\Seeder;
use Throwable;

class IngredientArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws Throwable
     */
    public function run(): void
    {
        $team = system_team();
        $store = $team->defaultStore();
        $volume = Unit::where('name', '=', 'Volume')->first();
        $liter = Unit::where('name', '=', 'Liter')->first();

        $ingredientFamily = [
            'Salt', 'Pepper', 'Basil', 'Oregano', 'Curry Powder', 'Ginger', 'Parsley',
            'Tomatoes', 'Onions', 'Garlic', 'Bell Peppers', 'Carrots', 'Potatoes',
            'Cheese', 'Butter', 'Sugar', 'Flour', 'Eggs', 'Bread', 'Lemon Juice',
            'Olive Oil', 'Soy Sauce', 'Ground Beef', 'Chicken Breast', 'Milk',
            'Coconut Milk', 'Beef Broth', 'Vegetable Broth', 'Rice', 'Pasta',
        ];

        collect($ingredientFamily)->each(function ($family) use ($volume, $liter, $store): void {
            $ingredient = Article::create([
                'is_profit_contributing' => true,
                'store_id' => $store->id,
                'unit_id' => $liter->id,
                'is_ingredient' => true,
                'is_reference' => true,
                'name' => $family,
            ]);

            $ingredient->appendNode(Article::create([
                'reorder_level' => fake()->numberBetween(1, 100),
                'unit_capacity' => fake()->numberBetween(1, 3),
                'unit_id' => $volume->descendants->random()->id,
                'is_profit_contributing' => true,
                'can_be_purchased' => true,
                'store_id' => $store->id,
                'is_ingredient' => true,
                'name' => $family,
            ]));
        });
    }
}
