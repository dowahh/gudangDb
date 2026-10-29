<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'sku' => 'SKU-'.fake()->unique()->numberBetween(10000, 99999),
            'name' => 'Barang Contoh '.fake()->unique()->numberBetween(1, 9999),
            'stock' => 0,
            'min_stock' => 5,
            'price' => fake()->randomFloat(2, 1000, 500000),
        ];
    }
}
