<?php

namespace Database\Seeders\Production;

use App\Models\Production\Restaurant;
use App\Models\Production\Station;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        Restaurant::create([
            'name' => 'Su Cafeteria',
            'team_id' => team_id(),
            'description' => 'Restaurant 1 description',
            'is_active' => true,
        ]);

        $names = [
            'Saucier Station', 'Entre metier Station', 'Fry Station', 'Pâtissier Station',
        ];

        collect($names)->each(function ($name): void {
            Station::create([
                'name' => $name,
                'team_id' => team_id(),
                'description' => $name . ' description',
                'is_active' => true,
            ]);
        });
    }
}
