<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\MaintenanceRecord;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceRecordFactory extends Factory
{
    protected $model = MaintenanceRecord::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'asset_id' => Asset::factory(),
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['scheduled', 'repair', 'inspection', 'other']),
            'scheduled_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'completed_date' => null,
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'description' => $this->faker->sentence(),
            'work_performed' => $this->faker->optional()->paragraph(),
            'cost' => $this->faker->optional()->randomFloat(2, 50, 500),
            'service_provider' => $this->faker->optional()->company(),
            'notes' => $this->faker->optional()->sentence(),
            'next_maintenance_date' => $this->faker->optional()->dateTimeBetween('+1 month', '+6 months'),
        ];
    }

    public function forOrganization($organizationId)
    {
        return $this->state(function (array $attributes) use ($organizationId) {
            return [
                'organization_id' => $organizationId,
            ];
        });
    }

    public function forAsset($assetId)
    {
        return $this->state(function (array $attributes) use ($assetId) {
            return [
                'asset_id' => $assetId,
            ];
        });
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'completed_date' => null,
            ];
        });
    }

    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'completed_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            ];
        });
    }
}
