<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\User;
use App\Mail\OrganizationInvitationMail;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\TestCaseBase;

class OrganizationControllerTest extends TestCaseBase
{

    /** @test */
    public function super_admin_can_view_organizations()
    {
        // TestCaseBase already creates 2 organizations, so we'll check for at least those
        $response = $this->actingAs($this->superAdmin)->getJson('/api/super-admin/organizations');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
        $this->assertGreaterThanOrEqual(2, count($response->json('data')));
    }

    /** @test */
    public function super_admin_can_create_organization()
    {
        Mail::fake();

        $data = [
            'name' => 'New Organization',
            'email' => 'admin@neworg.com'
        ];

        $response = $this->actingAs($this->superAdmin)->postJson('/api/super-admin/organizations', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('organizations', [
            'name' => 'New Organization',
            'email' => 'admin@neworg.com',
            'registration_source' => 'invited'
        ]);
        Mail::assertSent(OrganizationInvitationMail::class);
    }

    /** @test */
    public function organization_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->superAdmin)->postJson('/api/super-admin/organizations', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email']);
    }

    /** @test */
    public function super_admin_can_view_organization_details()
    {
        $organization = Organization::factory()->create();

        $response = $this->actingAs($this->superAdmin)->getJson("/api/super-admin/organizations/{$organization->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.organization.id', $organization->id);
    }

    /** @test */
    public function super_admin_can_update_organization()
    {
        $organization = Organization::factory()->create();

        $data = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
            'is_active' => true
        ];

        $response = $this->actingAs($this->superAdmin)->putJson("/api/super-admin/organizations/{$organization->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    /** @test */
    public function super_admin_can_delete_organization()
    {
        $organization = Organization::factory()->create();

        $response = $this->actingAs($this->superAdmin)->deleteJson("/api/super-admin/organizations/{$organization->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('organizations', ['id' => $organization->id]);
    }

    /** @test */
    public function super_admin_can_resend_invitation()
    {
        Mail::fake();
        $organization = Organization::factory()->create();
        $invitation = OrganizationInvitation::factory()->forOrganization($organization->id)->create([
            'email' => $organization->email
        ]);

        $response = $this->actingAs($this->superAdmin)->postJson("/api/super-admin/organizations/{$organization->id}/resend-invitation");

        $response->assertStatus(200);
        Mail::assertSent(OrganizationInvitationMail::class);
    }

    /** @test */
    public function non_super_admin_cannot_access_organizations()
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/super-admin/organizations');

        $response->assertStatus(403);
    }
}

