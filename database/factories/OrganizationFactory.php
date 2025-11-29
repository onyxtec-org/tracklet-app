<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition()
    {
        $name = $this->faker->company();
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'is_active' => true,
            'is_subscribed' => false,
            'registration_source' => 'self_registered',
            'trial_ends_at' => now()->addDays(30),
        ];
    }

    public function subscribed()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_subscribed' => true,
                'trial_ends_at' => null,
                'subscription_ends_at' => now()->addYear(),
            ];
        });
    }

    public function onTrial()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_subscribed' => false,
                'trial_ends_at' => now()->addDays(15),
            ];
        });
    }
}



