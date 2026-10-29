<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'name' => 'PT Contoh Supplier '.fake()->unique()->numberBetween(1, 9999),
            'phone' => '0'.fake()->numberBetween(8100000000, 8999999999),
            'address' => fake()->address(),
        ];
    }
}
