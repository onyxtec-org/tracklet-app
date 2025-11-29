<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\Feature\TestCaseBase;

class OrganizationInvitationControllerTest extends TestCaseBase
{

    /** @test */
    public function user_can_view_valid_invitation()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->create([
            'email' => 'newuser@example.com',
            'expires_at' => now()->addDays(7)
        ]);

        $response = $this->get("/invitation/{$invitation->token}");

        $response->assertStatus(200);
        $response->assertViewIs('auth.accept-invitation');
    }

    /** @test */
    public function user_cannot_view_expired_invitation()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->expired()->create([
            'email' => 'newuser@example.com'
        ]);

        $response = $this->getJson("/api/invitation/{$invitation->token}");

        $response->assertStatus(410);
        $response->assertJsonPath('message', 'This invitation has expired.');
    }

    /** @test */
    public function user_cannot_view_already_accepted_invitation()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->accepted()->create([
            'email' => 'newuser@example.com'
        ]);

        $response = $this->getJson("/api/invitation/{$invitation->token}");

        $response->assertStatus(400);
        $response->assertJsonPath('message', 'This invitation has already been accepted.');
    }

    /** @test */
    public function user_can_accept_invitation_and_create_account()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->create([
            'email' => 'newuser@example.com',
            'expires_at' => now()->addDays(7)
        ]);

        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson("/api/invitation/{$invitation->token}/accept", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
            'organization_id' => $this->organization->id
        ]);
        
        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertTrue($user->hasRole('admin'));
        $this->assertNotNull($invitation->fresh()->accepted_at);
    }

    /** @test */
    public function invitation_acceptance_validates_required_fields()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->create([
            'email' => 'newuser@example.com'
        ]);

        $response = $this->postJson("/api/invitation/{$invitation->token}/accept", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function invitation_acceptance_validates_email_matches_invitation()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->create([
            'email' => 'invited@example.com'
        ]);

        $data = [
            'name' => 'New User',
            'email' => 'different@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson("/api/invitation/{$invitation->token}/accept", $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function invitation_acceptance_validates_password_confirmation()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->create([
            'email' => 'newuser@example.com'
        ]);

        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different'
        ];

        $response = $this->postJson("/api/invitation/{$invitation->token}/accept", $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function user_cannot_accept_expired_invitation()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->expired()->create([
            'email' => 'newuser@example.com'
        ]);

        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson("/api/invitation/{$invitation->token}/accept", $data);

        $response->assertStatus(410);
        $response->assertJsonPath('message', 'This invitation has expired.');
    }

    /** @test */
    public function user_cannot_accept_already_accepted_invitation()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->accepted()->create([
            'email' => 'newuser@example.com'
        ]);

        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson("/api/invitation/{$invitation->token}/accept", $data);

        $response->assertStatus(400);
        $response->assertJsonPath('message', 'This invitation has already been accepted.');
    }

    /** @test */
    public function accepting_invitation_assigns_admin_role()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->create([
            'email' => 'newuser@example.com'
        ]);

        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson("/api/invitation/{$invitation->token}/accept", $data);

        $response->assertStatus(200);
        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertTrue($user->hasRole('admin'));
    }

    /** @test */
    public function accepting_invitation_logs_user_in()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->create([
            'email' => 'newuser@example.com'
        ]);

        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson("/api/invitation/{$invitation->token}/accept", $data);

        $response->assertStatus(200);
        $this->assertAuthenticated();
    }

    /** @test */
    public function accepting_invitation_updates_existing_user_if_email_exists()
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'organization_id' => null
        ]);

        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->create([
            'email' => 'existing@example.com'
        ]);

        $data = [
            'name' => 'Updated Name',
            'email' => 'existing@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->postJson("/api/invitation/{$invitation->token}/accept", $data);

        $response->assertStatus(200);
        $existingUser->refresh();
        $this->assertEquals($this->organization->id, $existingUser->organization_id);
        $this->assertEquals('Updated Name', $existingUser->name);
        $this->assertTrue(Hash::check('newpassword123', $existingUser->password));
    }

    /** @test */
    public function cannot_accept_invitation_if_user_belongs_to_different_organization()
    {
        $otherOrg = Organization::factory()->create();
        $existingUser = User::factory()->forOrganization($otherOrg->id)->create([
            'email' => 'existing@example.com'
        ]);

        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->create([
            'email' => 'existing@example.com'
        ]);

        $data = [
            'name' => 'Updated Name',
            'email' => 'existing@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->postJson("/api/invitation/{$invitation->token}/accept", $data);

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'This email is already associated with another organization.');
    }

    /** @test */
    public function api_can_view_invitation()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->create([
            'email' => 'newuser@example.com'
        ]);

        $response = $this->getJson("/api/invitation/{$invitation->token}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['invitation']]);
    }

    /** @test */
    public function api_can_accept_invitation()
    {
        $invitation = OrganizationInvitation::factory()->forOrganization($this->organization->id)->create([
            'email' => 'newuser@example.com'
        ]);

        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson("/api/invitation/{$invitation->token}/accept", $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['message', 'redirect']]);
    }
}

