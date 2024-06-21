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
            'description' => 'SU Cafeteria Team',
            'kfs_account_number' => '3001000',
            'head_user_id' => $head->id,
            'name' => 'SU Cafeteria',
            'is_default' => true,
        ]);

        $head->update(['team_id' => $team->id]);
        $data = User::factory(9)->make(['team_id' => $team->id])->toArray();
        $team->members()->createMany($data);

        User::all()->each(function ($user) use ($team): void {
            $user->teams()->attach($team->id);
        });
    }
}
