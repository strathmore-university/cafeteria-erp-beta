<?php

namespace Database\Seeders\Inventory;

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Inventory\Store;
use Illuminate\Database\Seeder;
use Throwable;

class InventoryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws Throwable
     */
    public function run(): void
    {
        $type = store_types()->random();

        $store = Store::create([
            'name' => 'Main Store',
            'team_id' => system_team()->id,
            'category_id' => $type->id,
            'owner_id' => system_team()->id,
            'owner_type' => system_team()->getMorphClass(),
        ]);

        $team = system_team();
        $piece = Unit::where('name', '=', 'Piece')->first();
        $volume = Unit::where('name', '=', 'Volume')->first();
        $portion = Unit::where('name', '=', 'Portion')->first();

        $liter = Unit::where('name', '=', 'Liter')->first();
        $ml = Unit::where('name', '=', 'Milliliter')->first();

        $consumable = Article::create([
            'is_profit_contributing' => true,
            'store_id' => $store->id,
            'name' => 'Soft Drinks',
            'is_consumable' => true,
            'unit_id' => $piece->id,
            'is_reference' => true,
            'team_id' => $team->id,
            'is_sellable' => true,
            'reorder_level' => 5,
        ]);

        foreach (range(1, 10) as $range) {
            $node = Article::create([
                'reorder_level' => fake()->numberBetween(1, 100),
                'is_profit_contributing' => true,
                'name' => fake()->country,
                'store_id' => $store->id,
                'is_consumable' => true,
                'unit_id' => $piece->id,
                'team_id' => $team->id,
                'is_sellable' => true,
                'unit_capacity' => 1,
            ]);

            $consumable->appendNode($node);
        }

        $ingredient = Article::create([
            'is_profit_contributing' => true,
            'store_id' => $store->id,
            'unit_id' => $liter->id,
            'is_ingredient' => true,
            'is_reference' => true,
            'team_id' => $team->id,
            'reorder_level' => 5,
            'name' => 'Milk',
        ]);

        foreach (range(1, 10) as $range) {
            $node = Article::create([
                'unit_id' => $volume->descendants->random()->id,
                //                'unit_id' => fake()->randomElement([$liter->id, $ml->id]),
                'reorder_level' => fake()->numberBetween(1, 100),
                'unit_capacity' => fake()->numberBetween(1, 3),
                'is_profit_contributing' => true,
                'name' => fake()->country,
                'store_id' => $store->id,
                'is_ingredient' => true,
                'team_id' => $team->id,
            ]);
            $ingredient->appendNode($node);
        }

        // todo: keep products under a reference or not
        $product = Article::create([
            'is_profit_contributing' => true,
            'unit_id' => $portion->id,
            'store_id' => $store->id,
            'is_reference' => true,
            'team_id' => $team->id,
            'is_product' => true,
            'name' => 'Products',
        ]);

        $node = Article::create([
            'is_profit_contributing' => true,
            'unit_id' => $portion->id,
            'store_id' => $store->id,
            'team_id' => $team->id,
            'is_product' => true,
            'name' => 'Pizza',
        ]);
        $product->appendNode($node);

        $node = Article::create([
            'is_profit_contributing' => true,
            'unit_id' => $portion->id,
            'store_id' => $store->id,
            'team_id' => $team->id,
            'is_product' => true,
            'name' => 'Pilau',
        ]);
        $product->appendNode($node);
    }
}
