<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\TestCaseBase;

class AuthControllerTest extends TestCaseBase
{

    /** @test */
    public function user_can_register_organization_via_api()
    {
        $data = [
            'organization_name' => 'New Company',
            'name' => 'John Doe',
            'email' => 'john@newcompany.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'data' => ['token', 'user']]);
        $this->assertDatabaseHas('organizations', [
            'name' => 'New Company',
            'email' => 'john@newcompany.com'
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'john@newcompany.com',
            'name' => 'John Doe'
        ]);
    }

    /** @test */
    public function registration_validates_required_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['organization_name', 'name', 'email', 'password']);
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

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
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

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function user_can_login_via_api()
    {
        $user = User::factory()->forOrganization($this->organization->id)->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['token', 'user']]);
        $this->assertNotNull($response->json('data.token'));
    }

    /** @test */
    public function login_validates_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'Invalid credentials.');
    }

    /** @test */
    public function login_returns_must_change_password_flag()
    {
        $user = User::factory()->forOrganization($this->organization->id)->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'must_change_password' => true
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('data.must_change_password'));
    }

    /** @test */
    public function authenticated_user_can_view_their_profile()
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJsonPath('data.user.id', $this->adminUser->id);
    }

    /** @test */
    public function user_can_logout_via_api()
    {
        $token = $this->adminUser->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $this->adminUser->id,
            'name' => 'test-token'
        ]);
    }

    /** @test */
    public function user_can_change_password_via_api()
    {
        $user = User::factory()->forOrganization($this->organization->id)->create([
            'password' => Hash::make('oldpassword'),
            'must_change_password' => true
        ]);

        $response = $this->actingAs($user)->postJson('/api/change-password', [
            'current_password' => 'oldpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(200);
        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function password_change_validates_current_password()
    {
        $user = User::factory()->forOrganization($this->organization->id)->create([
            'password' => Hash::make('oldpassword')
        ]);

        $response = $this->actingAs($user)->postJson('/api/change-password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(422);
        // The error might be in current_password validation or the response structure
        $this->assertTrue($response->json('success') === false || isset($response->json()['errors']));
    }

    /** @test */
    public function password_change_validates_new_password_confirmation()
    {
        $user = User::factory()->forOrganization($this->organization->id)->create([
            'password' => Hash::make('oldpassword')
        ]);

        $response = $this->actingAs($user)->postJson('/api/change-password', [
            'current_password' => 'oldpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'different'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['new_password']);
    }
}

