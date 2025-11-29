<?php

namespace Database\Factories;

use App\Models\StockTransaction;
use App\Models\Organization;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockTransactionFactory extends Factory
{
    protected $model = StockTransaction::class;

    public function definition()
    {
        return [
            'organization_id' => Organization::factory(),
            'inventory_item_id' => InventoryItem::factory(),
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['in', 'out']),
            'quantity' => $this->faker->numberBetween(1, 100),
            'transaction_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'reference' => $this->faker->optional()->bothify('PO-####'),
            'notes' => $this->faker->optional()->sentence(),
            'unit_price' => $this->faker->optional()->randomFloat(2, 1, 100),
            'vendor' => $this->faker->optional()->company(),
        ];
    }

    public function stockIn()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'in',
                'unit_price' => $this->faker->randomFloat(2, 1, 100),
                'vendor' => $this->faker->company(),
            ];
        });
    }

    public function stockOut()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'out',
                'unit_price' => null,
                'vendor' => null,
            ];
        });
    }
}



