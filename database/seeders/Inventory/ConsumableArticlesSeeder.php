<?php

namespace Database\Seeders\Inventory;

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use Illuminate\Database\Seeder;
use Throwable;

class ConsumableArticlesSeeder extends Seeder
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
        $piece = Unit::where('name', '=', 'Piece')->first();

        $consumables = [
            'Soda', 'Yogurt', 'Ketchup', 'Mustard', 'Mayonnaise', 'Salad Dressing',
            'BBQ Sauce', 'Juice', 'Water Bottle', 'Energy Drink', 'Milkshake', 'Coffee',
            'Tea', 'Hot Chocolate', 'Smoothie', 'Ice Cream', 'Chips', 'Pretzels',
            'Chocolate Bar', 'Candy',
        ];

        collect($consumables)->each(function ($item) use ($piece, $store): void {
            Article::create([
                'reorder_level' => fake()->numberBetween(1, 100),
                'is_profit_contributing' => true,
                'can_be_purchased' => true,
                'store_id' => $store->id,
                'is_consumable' => true,
                'unit_id' => $piece->id,
                'team_id' => team_id(),
                'is_sellable' => true,
                'unit_capacity' => 1,
                'name' => $item,
            ]);
        });
    }
}
