<?php

namespace Database\Seeders\Base;

use Database\Seeders\Core\CoreDatabaseSeeder;
use Illuminate\Database\Seeder;

class FoundationSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            KfsVendorSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            CoreDatabaseSeeder::class,
            SettingsSeeder::class
        ]);
    }
}
