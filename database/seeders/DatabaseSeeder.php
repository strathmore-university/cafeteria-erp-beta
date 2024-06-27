<?php

namespace Database\Seeders;

use Database\Seeders\Base\FoundationSeeder;
use Database\Seeders\Inventory\ConsumableArticlesSeeder;
use Database\Seeders\Inventory\IngredientArticlesSeeder;
use Database\Seeders\Procurement\ProcurementDatabaseSeeder;
use Database\Seeders\Production\ProductionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FoundationSeeder::class,
            ConsumableArticlesSeeder::class,
            IngredientArticlesSeeder::class,
            ProcurementDatabaseSeeder::class,
            //            StockTakeSeeder::class,
            ProductionSeeder::class,
        ]);
    }
}
