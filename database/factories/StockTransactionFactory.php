<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockTransaction>
 */
class StockTransactionFactory extends Factory
{
    protected $model = StockTransaction::class;

    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'user_id' => User::factory(),
            'supplier_id' => null,
            'type' => 'masuk',
            'qty' => fake()->numberBetween(1, 20),
            'note' => null,
            'transaction_date' => now()->toDateString(),
        ];
    }

    public function masuk(): static
    {
        return $this->state(fn () => ['type' => 'masuk']);
    }

    public function keluar(): static
    {
        return $this->state(fn () => ['type' => 'keluar', 'supplier_id' => null]);
    }
}
