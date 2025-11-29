<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\InventoryItem;
use App\Models\Asset;
use App\Models\MaintenanceRecord;
use App\Models\Organization;
use App\Models\User;
use Tests\Feature\TestCaseBase;

class DashboardControllerTest extends TestCaseBase
{
    /** @test */
    public function super_admin_can_view_dashboard_with_system_stats()
    {
        Organization::factory()->count(3)->create();
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->superAdmin)->getJson('/api/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['super_admin_stats']]);
        $this->assertArrayHasKey('total_organizations', $response->json('data.super_admin_stats'));
        $this->assertArrayHasKey('total_users', $response->json('data.super_admin_stats'));
    }

    /** @test */
    public function admin_can_view_dashboard_with_organization_stats()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        Expense::factory()->count(3)->forOrganization($this->organization->id)->forCategory($category->id)->create();
        InventoryItem::factory()->count(2)->forOrganization($this->organization->id)->create();
        Asset::factory()->count(2)->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson('/api/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['financial_snapshot', 'inventory_status', 'asset_summary']]);
    }

    /** @test */
    public function finance_can_view_dashboard_with_financial_data()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        Expense::factory()->count(5)->forOrganization($this->organization->id)->forCategory($category->id)->create();

        $response = $this->actingAs($this->financeUser)->getJson('/api/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['financial_snapshot', 'expense_charts']]);
    }

    /** @test */
    public function admin_support_can_view_dashboard_with_inventory_and_assets()
    {
        InventoryItem::factory()->count(3)->forOrganization($this->organization->id)->create();
        Asset::factory()->count(3)->forOrganization($this->organization->id)->create();
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();
        MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->pending()->create([
            'scheduled_date' => now()->addDays(3)
        ]);

        $response = $this->actingAs($this->adminSupportUser)->getJson('/api/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['inventory_status', 'asset_summary', 'upcoming_maintenance']]);
    }

    /** @test */
    public function dashboard_shows_trial_info_for_organization_on_trial()
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/dashboard');

        $response->assertStatus(200);
        $this->assertArrayHasKey('trial_info', $response->json('data'));
        $this->assertTrue($response->json('data.trial_info.is_on_trial'));
    }

    /** @test */
    public function dashboard_calculates_financial_snapshot_correctly()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        Expense::factory()->count(3)->forOrganization($this->organization->id)->forCategory($category->id)->create([
            'expense_date' => now(),
            'amount' => 100
        ]);
        Expense::factory()->count(2)->forOrganization($this->organization->id)->forCategory($category->id)->create([
            'expense_date' => now()->subMonth(),
            'amount' => 50
        ]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/dashboard');

        $response->assertStatus(200);
        $financialSnapshot = $response->json('data.financial_snapshot');
        $this->assertEquals(300, $financialSnapshot['current_month']);
        $this->assertEquals(100, $financialSnapshot['previous_month']);
    }

    /** @test */
    public function dashboard_shows_low_stock_items()
    {
        InventoryItem::factory()->lowStock()->forOrganization($this->organization->id)->create();
        InventoryItem::factory()->forOrganization($this->organization->id)->create(['quantity' => 100]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/dashboard');

        $response->assertStatus(200);
        $inventoryStatus = $response->json('data.inventory_status');
        $this->assertGreaterThan(0, $inventoryStatus['low_stock_count']);
    }

    /** @test */
    public function dashboard_shows_upcoming_maintenance()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();
        MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->pending()->create([
            'scheduled_date' => now()->addDays(3)
        ]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/dashboard');

        $response->assertStatus(200);
        $upcomingMaintenance = $response->json('data.upcoming_maintenance');
        $this->assertNotEmpty($upcomingMaintenance);
    }
}

