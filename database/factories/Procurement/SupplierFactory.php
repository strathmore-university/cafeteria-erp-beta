<?php

namespace Database\Factories\Procurement;

use App\Models\Procurement\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'description' => fake()->sentence,
            'email' => fake()->email,
            'phone_number' => fake()->phoneNumber,
            'address' => fake()->address,
            'kfs_preformat_description' => fake()->words(2),
            'kfs_preformat_code' => fake()->slug(1),
            'percentage_vat' => 16,
            'kfs_vendor_number' => fake()->numberBetween(10000, 20000),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
