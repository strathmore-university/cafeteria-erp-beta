<?php

namespace Database\Seeders\Base;

use App\Models\Procurement\KfsVendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Random\RandomException;

class KfsVendorSeeder extends Seeder
{
    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $params = match (app()->isLocal()) {
            false => ['--limit' => null, '--size' => 1000],
            true => ['--limit' => 1, '--size' => 10]
        };

        //        $params = array_merge($params, ['-q' => true]);
        //        Artisan::call('fetch:kfs-vendors', $params);

        foreach (range(1, 10) as $item) {
            KfsVendor::create([
                'pre_format_description' => fake()->word(),
                'pre_format_code' => fake()->slug,
                'vendor_name' => fake()->name,
                'vendor_number' => random_int(1000, 9999),
            ]);
        }
    }
}
