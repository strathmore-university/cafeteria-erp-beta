<?php

namespace Database\Seeders;

use Database\Seeders\Core\CoreDatabaseSeeder;
use Database\Seeders\Inventory\InventoryDatabaseSeeder;
use Database\Seeders\Procurement\ProcurementDatabaseSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CoreDatabaseSeeder::class,
            InventoryDatabaseSeeder::class,
            ProcurementDatabaseSeeder::class,
        ]);
    }
}
