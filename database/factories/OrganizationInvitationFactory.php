<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationInvitationFactory extends Factory
{
    protected $model = OrganizationInvitation::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'email' => $this->faker->unique()->safeEmail(),
            'token' => \Illuminate\Support\Str::random(64),
            'invited_by' => User::factory(),
            'accepted_at' => null,
            'expires_at' => $this->faker->dateTimeBetween('now', '+7 days'),
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

    public function accepted()
    {
        return $this->state(function (array $attributes) {
            return [
                'accepted_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            ];
        });
    }

    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
            ];
        });
    }
}
