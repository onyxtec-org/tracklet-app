<?php

namespace Tests\Feature;

use App\Models\User;
use App\Mail\UserInvitationMail;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\TestCaseBase;

class UserControllerTest extends TestCaseBase
{

    /** @test */
    public function admin_can_view_users()
    {
        $user1 = User::factory()->forOrganization($this->organization->id)->create();
        $user1->assignRole('finance');
        $user2 = User::factory()->forOrganization($this->organization->id)->create();
        $user2->assignRole('admin_support');
        $otherUser = User::factory()->forOrganization($this->otherOrganization->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['users', 'roles']]);
        $userIds = collect($response->json('data.users.data'))->pluck('id')->toArray();
        $this->assertContains($user1->id, $userIds);
        $this->assertContains($user2->id, $userIds);
        $this->assertNotContains($otherUser->id, $userIds);
    }

    /** @test */
    public function users_can_be_filtered_by_role()
    {
        $financeUser = User::factory()->forOrganization($this->organization->id)->create();
        $financeUser->assignRole('finance');
        $adminUser2 = User::factory()->forOrganization($this->organization->id)->create();
        $adminUser2->assignRole('admin');

        $response = $this->actingAs($this->adminUser)->getJson('/api/users?role=finance');

        $response->assertStatus(200);
        $userIds = collect($response->json('data.users.data'))->pluck('id')->toArray();
        $this->assertContains($financeUser->id, $userIds);
        $this->assertNotContains($adminUser2->id, $userIds);
    }

    /** @test */
    public function users_can_be_searched()
    {
        $user1 = User::factory()->forOrganization($this->organization->id)->create(['name' => 'John Doe']);
        $user2 = User::factory()->forOrganization($this->organization->id)->create(['name' => 'Jane Smith']);

        $response = $this->actingAs($this->adminUser)->getJson('/api/users?search=John');

        $response->assertStatus(200);
        $userIds = collect($response->json('data.users.data'))->pluck('id')->toArray();
        $this->assertContains($user1->id, $userIds);
        $this->assertNotContains($user2->id, $userIds);
    }

    /** @test */
    public function admin_can_create_user()
    {
        Mail::fake();

        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'role' => 'finance'
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/users', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'organization_id' => $this->organization->id,
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'must_change_password' => true
        ]);
        
        $newUser = User::where('email', 'newuser@example.com')->first();
        $this->assertTrue($newUser->hasRole('finance'));
        Mail::assertSent(UserInvitationMail::class);
    }

    /** @test */
    public function user_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->adminUser)->postJson('/api/users', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'role']);
    }

    /** @test */
    public function user_creation_validates_email_is_unique()
    {
        User::factory()->forOrganization($this->organization->id)->create(['email' => 'existing@example.com']);

        $data = [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'role' => 'finance'
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/users', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function admin_cannot_assign_super_admin_role()
    {
        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'role' => 'super_admin'
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/users', $data);

        $response->assertStatus(403);
        $response->assertJsonPath('message', 'Cannot assign super_admin role.');
    }

    /** @test */
    public function admin_can_view_user_details()
    {
        $user = User::factory()->forOrganization($this->organization->id)->create();
        $user->assignRole('finance');

        $response = $this->actingAs($this->adminUser)->getJson("/api/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.user.id', $user->id);
    }

    /** @test */
    public function admin_cannot_view_other_organization_user()
    {
        $user = User::factory()->forOrganization($this->otherOrganization->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson("/api/users/{$user->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_user()
    {
        $user = User::factory()->forOrganization($this->organization->id)->create();
        $user->assignRole('finance');

        $data = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'admin_support'
        ];

        $response = $this->actingAs($this->adminUser)->putJson("/api/users/{$user->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
        $user->refresh();
        $this->assertTrue($user->hasRole('admin_support'));
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $user = User::factory()->forOrganization($this->organization->id)->create();
        $user->assignRole('finance');

        $response = $this->actingAs($this->adminUser)->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function admin_cannot_delete_themselves()
    {
        $response = $this->actingAs($this->adminUser)->deleteJson("/api/users/{$this->adminUser->id}");

        $response->assertStatus(403);
        $response->assertJsonPath('message', 'Cannot delete your own account.');
    }

    /** @test */
    public function finance_cannot_access_user_management()
    {
        $response = $this->actingAs($this->financeUser)->getJson('/api/users');

        $response->assertStatus(403);
    }
}

