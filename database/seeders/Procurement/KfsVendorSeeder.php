<?php

namespace Database\Seeders\Procurement;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class KfsVendorSeeder extends Seeder
{
    public function run(): void
    {
        $params = match (app()->isLocal()) {
            false => ['--limit' => null, '--size' => 1000],
            true => ['--limit' => 1, '--size' => 10]
        };

        $params = array_merge($params, ['-q' => true]);
        Artisan::call('fetch:kfs-vendors', $params);
    }
}
