<?php

namespace Database\Seeders;

use Database\Seeders\Procurement\KfsVendorSeeder;
use Illuminate\Database\Seeder;

class FoundationSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            KfsVendorSeeder::class,
        ]);
    }
}
