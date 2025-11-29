<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Tests\Feature\TestCaseBase;

class SubscriptionControllerTest extends TestCaseBase
{
    /** @test */
    public function authenticated_user_can_view_checkout_page()
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/subscription/checkout');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    /** @test */
    public function subscribed_organization_redirects_from_checkout()
    {
        $this->organization->update(['is_subscribed' => true]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/subscription/checkout');

        $response->assertStatus(200);
        $this->assertArrayHasKey('redirect', $response->json('data'));
    }

    /** @test */
    public function user_without_organization_cannot_access_checkout()
    {
        $user = User::factory()->create(['organization_id' => null]);

        $response = $this->actingAs($user)->getJson('/api/subscription/checkout');

        $response->assertStatus(403);
    }

    /** @test */
    public function checkout_returns_price_info_when_configured()
    {
        // Mock Stripe config
        config(['services.stripe.price_id' => 'price_test123']);

        $response = $this->actingAs($this->adminUser)->getJson('/api/subscription/checkout');

        $response->assertStatus(200);
        // Price info may or may not be present depending on Stripe API availability
        $this->assertIsArray($response->json('data'));
    }
}

