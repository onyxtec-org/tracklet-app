<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetMovementFactory extends Factory
{
    protected $model = AssetMovement::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'asset_id' => Asset::factory(),
            'user_id' => User::factory(),
            'movement_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'movement_type' => $this->faker->randomElement(['assignment', 'location_change', 'return', 'other']),
            'from_user_id' => null,
            'from_location' => null,
            'to_user_id' => $this->faker->optional()->randomElement([User::factory()]),
            'to_location' => $this->faker->optional()->randomElement(['Room 101', 'Room 202', 'Office A', 'Warehouse']),
            'reason' => $this->faker->sentence(),
            'notes' => $this->faker->optional()->sentence(),
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

    public function assignment()
    {
        return $this->state(function (array $attributes) {
            return [
                'movement_type' => 'assignment',
                'to_user_id' => User::factory(),
            ];
        });
    }

    public function locationChange()
    {
        return $this->state(function (array $attributes) {
            return [
                'movement_type' => 'location_change',
                'from_location' => 'Room 101',
                'to_location' => 'Room 202',
            ];
        });
    }
}
