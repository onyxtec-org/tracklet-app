<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryItemFactory extends Factory
{
    protected $model = InventoryItem::class;

    public function definition()
    {
        $unitPrice = $this->faker->randomFloat(2, 1, 100);
        $quantity = $this->faker->numberBetween(0, 500);
        $totalPrice = round($quantity * $unitPrice, 2);
        
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->words(3, true),
            'category' => $this->faker->randomElement(['Office Supplies', 'Electronics', 'Furniture', 'Stationery', 'Tools']),
            'quantity' => $quantity,
            'minimum_threshold' => $this->faker->numberBetween(5, 50),
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'unit' => $this->faker->randomElement(['pieces', 'boxes', 'reams', 'units', 'packs']),
        ];
    }

    public function lowStock()
    {
        return $this->state(function (array $attributes) {
            $threshold = $attributes['minimum_threshold'] ?? 20;
            return [
                'quantity' => $this->faker->numberBetween(0, $threshold),
            ];
        });
    }

    public function forOrganization($organizationId)
    {
        return $this->state(function (array $attributes) use ($organizationId) {
            return [
                'organization_id' => $organizationId,
            ];
        });
    }
}

