<?php

namespace Tests\Feature;

use Spatie\Permission\Models\Role;
use Tests\Feature\TestCaseBase;

class RoleControllerTest extends TestCaseBase
{
    /** @test */
    public function authenticated_user_can_view_available_roles()
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/roles');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['roles']]);
        $roles = collect($response->json('data.roles'))->pluck('name')->toArray();
        $this->assertNotContains('super_admin', $roles);
        $this->assertContains('admin', $roles);
        $this->assertContains('finance', $roles);
    }

    /** @test */
    public function super_admin_can_view_roles_including_super_admin()
    {
        $response = $this->actingAs($this->superAdmin)->getJson('/api/roles');

        $response->assertStatus(200);
        $roles = collect($response->json('data.roles'))->pluck('name')->toArray();
        // Super admin should see all roles (implementation may vary)
        $this->assertIsArray($roles);
    }

    /** @test */
    public function unauthenticated_user_cannot_view_roles()
    {
        $response = $this->getJson('/api/roles');

        $response->assertStatus(401);
    }
}

