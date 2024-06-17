<?php

namespace Database\Seeders\Inventory;

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Inventory\StockLevel;
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
            'name' => 'Soft Drinks',
            'is_reference' => true,
            'is_consumable' => true,
            'team_id' => $team->id,
            'store_id' => $store->id,
            'unit_id' => $piece->id,
            'reorder_level' => 5,
            'is_profit_contributing' => true,
            'is_sellable' => true,
        ]);

        foreach (range(1, 10) as $range) {
            $node = Article::create([
                'name' => fake()->country,
                'is_consumable' => true,
                'team_id' => $team->id,
                'store_id' => $store->id,
                'unit_id' => $piece->id,
                'unit_capacity' => 1,
                'reorder_level' => fake()->numberBetween(1, 100),
                'is_profit_contributing' => true,
                'is_sellable' => true,
            ]);

            $consumable->appendNode($node);
        }

        $ingredient = Article::create([
            'is_profit_contributing' => true,
            'store_id' => $store->id,
            'unit_id' => $ml->id,
            'is_ingredient' => true,
            'is_reference' => true,
            'team_id' => $team->id,
            'reorder_level' => 5,
            'name' => 'Milk',
        ]);

        foreach (range(1, 10) as $range) {
            $node = Article::create([
                'name' => fake()->country,
                'is_ingredient' => true,
                'team_id' => $team->id,
                'store_id' => $store->id,
                'unit_id' => $liter->id,
                'unit_capacity' => 10 ?? fake()->randomElement([100, 250, 10, 5, 1000]),
                'reorder_level' => fake()->numberBetween(1, 100),
                'is_profit_contributing' => true,
            ]);

            $ingredient->appendNode($node);
        }

        $product = Article::create([
            'is_profit_contributing' => true,
            'store_id' => $store->id,
            'unit_id' => $portion->id,
            'is_product' => true,
            'is_reference' => true,
            'team_id' => $team->id,
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

        $articles = Article::isDescendant()->get();

        //        $articles->each(function (Article $article): void {
        //            StockLevel::create([
        //                'article_id' => $article->id,
        //                'store_id' => $article->store_id,
        //                'current_units' => random_int(1, 1000),
        //                'previous_units' => random_int(1, 1000),
        //            ]);
        //        });

        //        article_capacity($consumable);
    }
}
