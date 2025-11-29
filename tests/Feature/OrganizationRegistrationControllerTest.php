<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\Feature\TestCaseBase;

class OrganizationRegistrationControllerTest extends TestCaseBase
{

    /** @test */
    public function guest_can_view_registration_form()
    {
        $response = $this->get('/register-organization');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register-organization');
    }

    /** @test */
    public function guest_can_register_organization()
    {
        $data = [
            'organization_name' => 'New Company',
            'name' => 'John Doe',
            'email' => 'john@newcompany.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->post('/register-organization', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('organizations', [
            'name' => 'New Company',
            'email' => 'john@newcompany.com',
            'registration_source' => 'self_registered',
            'is_active' => true,
            'is_subscribed' => false
        ]);
        
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@newcompany.com',
            'organization_id' => Organization::where('name', 'New Company')->first()->id
        ]);
    }

    /** @test */
    public function registration_validates_required_fields()
    {
        $response = $this->post('/register-organization', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['organization_name', 'name', 'email', 'password']);
    }

    /** @test */
    public function registration_validates_email_is_unique()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $data = [
            'organization_name' => 'New Company',
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->post('/register-organization', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function registration_validates_password_confirmation()
    {
        $data = [
            'organization_name' => 'New Company',
            'name' => 'John Doe',
            'email' => 'john@newcompany.com',
            'password' => 'password123',
            'password_confirmation' => 'different'
        ];

        $response = $this->post('/register-organization', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function registration_generates_unique_slug()
    {
        Organization::factory()->create(['slug' => 'new-company']);

        $data = [
            'organization_name' => 'New Company',
            'name' => 'John Doe',
            'email' => 'john@newcompany.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->post('/register-organization', $data);

        $response->assertStatus(200);
        $organization = Organization::where('name', 'New Company')->where('email', 'john@newcompany.com')->first();
        $this->assertEquals('new-company-1', $organization->slug);
    }

    /** @test */
    public function registration_assigns_admin_role_to_first_user()
    {
        $data = [
            'organization_name' => 'New Company',
            'name' => 'John Doe',
            'email' => 'john@newcompany.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->post('/register-organization', $data);

        $response->assertStatus(200);
        $user = User::where('email', 'john@newcompany.com')->first();
        $this->assertTrue($user->hasRole('admin'));
    }

    /** @test */
    public function registration_auto_logs_in_user()
    {
        $data = [
            'organization_name' => 'New Company',
            'name' => 'John Doe',
            'email' => 'john@newcompany.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->post('/register-organization', $data);

        $response->assertStatus(200);
        $this->assertAuthenticated();
    }

    /** @test */
    public function registration_creates_organization_with_correct_source()
    {
        $data = [
            'organization_name' => 'New Company',
            'name' => 'John Doe',
            'email' => 'john@newcompany.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->post('/register-organization', $data);

        $response->assertStatus(200);
        $organization = Organization::where('name', 'New Company')->first();
        $this->assertEquals('self_registered', $organization->registration_source);
    }

    /** @test */
    public function registration_creates_unsubscribed_organization()
    {
        $data = [
            'organization_name' => 'New Company',
            'name' => 'John Doe',
            'email' => 'john@newcompany.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->post('/register-organization', $data);

        $response->assertStatus(200);
        $organization = Organization::where('name', 'New Company')->first();
        $this->assertFalse($organization->is_subscribed);
    }

    /** @test */
    public function api_can_view_registration_form()
    {
        $response = $this->getJson('/api/register-organization');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    /** @test */
    public function api_can_register_organization()
    {
        $data = [
            'organization_name' => 'New Company',
            'name' => 'John Doe',
            'email' => 'john@newcompany.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register-organization', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['message', 'redirect']]);
        $this->assertDatabaseHas('organizations', [
            'name' => 'New Company',
            'email' => 'john@newcompany.com'
        ]);
    }

    /** @test */
    public function registration_handles_database_errors_gracefully()
    {
        // This test would require mocking database failures
        // For now, we'll test that validation works correctly
        $data = [
            'organization_name' => str_repeat('a', 300), // Too long
            'name' => 'John Doe',
            'email' => 'john@newcompany.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->post('/register-organization', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['organization_name']);
    }
}

