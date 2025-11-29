<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

abstract class TestCaseBase extends TestCase
{
    use DatabaseTransactions;

    protected $organization;
    protected $otherOrganization;
    protected $adminUser;
    protected $adminSupportUser;
    protected $financeUser;
    protected $generalStaffUser;
    protected $superAdmin;
    protected $otherOrgUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles (use firstOrCreate to avoid duplicates)
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin_support', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'finance', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'general_staff', 'guard_name' => 'web']);

        // Create organizations
        $this->organization = Organization::factory()->onTrial()->create();
        $this->otherOrganization = Organization::factory()->onTrial()->create();

        // Create users
        $this->adminUser = User::factory()->forOrganization($this->organization->id)->create();
        $this->adminUser->assignRole('admin');

        $this->adminSupportUser = User::factory()->forOrganization($this->organization->id)->create();
        $this->adminSupportUser->assignRole('admin_support');

        $this->financeUser = User::factory()->forOrganization($this->organization->id)->create();
        $this->financeUser->assignRole('finance');

        $this->generalStaffUser = User::factory()->forOrganization($this->organization->id)->create();
        $this->generalStaffUser->assignRole('general_staff');

        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->otherOrgUser = User::factory()->forOrganization($this->otherOrganization->id)->create();
        $this->otherOrgUser->assignRole('admin');
    }
}

