<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseCategoryFactory extends Factory
{
    protected $model = ExpenseCategory::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->optional()->sentence(),
            'is_system' => false,
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

    public function system()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_system' => true,
            ];
        });
    }
}
