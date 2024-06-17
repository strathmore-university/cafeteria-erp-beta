<?php

namespace Database\Seeders\Core;

use App\Models\Core\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            UnitSeeder::class,
        ]);

        Artisan::call('make:filament-user', ['--name' => 'tony', '--email' => 'tony@gmail.com', '--password' => '12345678']);

        $head = User::first();

        $team = Team::create([
            'description' => 'Cafeteria Erp',
            'head_user_id' => $head->id,
            'name' => 'Cafeteria Erp',
            'is_default' => true,
        ]);

        $head->teams()->attach($team->id);

        $data = User::factory(9)->make(['team_id' => $team->id])->toArray();
        $team->members()->createMany($data);
    }
}
