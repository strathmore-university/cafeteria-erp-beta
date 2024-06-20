<?php

namespace Database\Seeders;

use Database\Seeders\Core\CoreDatabaseSeeder;
use Database\Seeders\Inventory\InventoryDatabaseSeeder;
use Database\Seeders\Inventory\StockTakeSeeder;
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
            CoreDatabaseSeeder::class,
            InventoryDatabaseSeeder::class,
            ProcurementDatabaseSeeder::class,
            StockTakeSeeder::class,
            ProductionSeeder::class,
        ]);
    }
}
