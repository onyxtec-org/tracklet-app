<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'expense_category_id' => ExpenseCategory::factory(),
            'user_id' => User::factory(),
            'expense_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'vendor_payee' => $this->faker->optional()->company(),
            'description' => $this->faker->optional()->sentence(),
            'receipt_path' => null,
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

    public function forCategory($categoryId)
    {
        return $this->state(function (array $attributes) use ($categoryId) {
            return [
                'expense_category_id' => $categoryId,
            ];
        });
    }
}
