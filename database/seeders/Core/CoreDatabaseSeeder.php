<?php

namespace Database\Seeders\Core;

use App\Models\Core\Department;
use App\Models\Core\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class CoreDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $head = User::create([
            'email' => 'system@strathmore.edu',
            'user_number' => 'system_user',
            'username' => 'system_user',
            'first_name' => 'System',
            'password' => '12345678',
            'is_system_user' => true,
            'last_name' => 'User',
            'is_staff' => false,
        ]);

        $team = Team::create([
            'description' => 'SU Cafeteria Team',
            'kfs_account_number' => '3001000',
            'head_user_id' => $head->id,
            'name' => 'SU Cafeteria',
            'is_default' => true,
        ]);

        $head->update(['team_id' => $team->id]);

        Artisan::call('seed:departments');

        $codes = collect([
            ['code' => 'iLab', 'sync' => '49'],
            ['code' => 'SBS', 'sync' => '24'],
            ['code' => 'DOS', 'sync' => '32'],
            ['code' => 'DVC - R&I', 'sync' => '12'],
            ['code' => 'SIMS', 'sync' => '45'],
            ['code' => 'SLS', 'sync' => '52'],
            ['code' => 'SIPPG', 'sync' => '122'],
            ['code' => 'ADMISSIONS', 'sync' => '48'],
        ]);

        $departments = Department::whereIn('code', $codes->pluck('code')->toArray())->get();

        $departments->each(function ($department) use ($codes) {
            $value = $department->getAttribute('code');
            $code = $codes->firstWhere('code', $value);
            $department->update(['sync_id' => $code['sync']]);
        });
    }
}
