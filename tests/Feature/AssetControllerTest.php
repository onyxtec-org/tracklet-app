<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\MaintenanceRecord;
use Tests\Feature\TestCaseBase;

class AssetControllerTest extends TestCaseBase
{
    /** @test */
    public function admin_can_view_assets()
    {
        $asset1 = Asset::factory()->forOrganization($this->organization->id)->create();
        $asset2 = Asset::factory()->forOrganization($this->organization->id)->create();
        $otherAsset = Asset::factory()->forOrganization($this->otherOrganization->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson('/api/assets');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['assets', 'summary']]);
        $assetIds = collect($response->json('data.assets.data'))->pluck('id')->toArray();
        $this->assertContains($asset1->id, $assetIds);
        $this->assertContains($asset2->id, $assetIds);
        $this->assertNotContains($otherAsset->id, $assetIds);
    }

    /** @test */
    public function admin_support_can_view_assets()
    {
        Asset::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->adminSupportUser)->getJson('/api/assets');

        $response->assertStatus(200);
    }

    /** @test */
    public function general_staff_can_view_assets()
    {
        Asset::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->generalStaffUser)->getJson('/api/view/assets');

        $response->assertStatus(200);
    }

    /** @test */
    public function assets_can_be_filtered_by_status()
    {
        $activeAsset = Asset::factory()->forOrganization($this->organization->id)->create(['status' => 'active']);
        $repairAsset = Asset::factory()->forOrganization($this->organization->id)->create(['status' => 'in_repair']);

        $response = $this->actingAs($this->adminUser)->getJson('/api/assets?status=active');

        $response->assertStatus(200);
        $assetIds = collect($response->json('data.assets.data'))->pluck('id')->toArray();
        $this->assertContains($activeAsset->id, $assetIds);
        $this->assertNotContains($repairAsset->id, $assetIds);
    }

    /** @test */
    public function assets_can_be_filtered_by_category()
    {
        $asset1 = Asset::factory()->forOrganization($this->organization->id)->create(['category' => 'Electronics']);
        $asset2 = Asset::factory()->forOrganization($this->organization->id)->create(['category' => 'Furniture']);

        $response = $this->actingAs($this->adminUser)->getJson('/api/assets?category=Electronics');

        $response->assertStatus(200);
        $assetIds = collect($response->json('data.assets.data'))->pluck('id')->toArray();
        $this->assertContains($asset1->id, $assetIds);
        $this->assertNotContains($asset2->id, $assetIds);
    }

    /** @test */
    public function assets_can_be_filtered_by_assigned_user()
    {
        $asset1 = Asset::factory()->forOrganization($this->organization->id)->create([
            'assigned_to_user_id' => $this->adminUser->id
        ]);
        $asset2 = Asset::factory()->forOrganization($this->organization->id)->create([
            'assigned_to_user_id' => $this->adminSupportUser->id
        ]);

        $response = $this->actingAs($this->adminUser)->getJson("/api/assets?assigned_to_user_id={$this->adminUser->id}");

        $response->assertStatus(200);
        $assetIds = collect($response->json('data.assets.data'))->pluck('id')->toArray();
        $this->assertContains($asset1->id, $assetIds);
        $this->assertNotContains($asset2->id, $assetIds);
    }

    /** @test */
    public function assets_can_be_searched()
    {
        $asset1 = Asset::factory()->forOrganization($this->organization->id)->create(['name' => 'Laptop Dell']);
        $asset2 = Asset::factory()->forOrganization($this->organization->id)->create(['name' => 'Desktop HP']);

        $response = $this->actingAs($this->adminUser)->getJson('/api/assets?search=Dell');

        $response->assertStatus(200);
        $assetIds = collect($response->json('data.assets.data'))->pluck('id')->toArray();
        $this->assertContains($asset1->id, $assetIds);
        $this->assertNotContains($asset2->id, $assetIds);
    }

    /** @test */
    public function admin_can_create_asset()
    {
        $data = [
            'name' => 'Test Asset',
            'category' => 'Electronics',
            'purchase_date' => '2025-01-15',
            'purchase_price' => 1200.00,
            'vendor' => 'Test Vendor',
            'warranty_expiry' => '2026-01-15',
            'serial_number' => 'SN123456',
            'model_number' => 'MODEL-001'
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/assets', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'data' => ['asset']]);
        $this->assertDatabaseHas('assets', [
            'organization_id' => $this->organization->id,
            'name' => 'Test Asset',
            'category' => 'Electronics',
            'purchase_price' => 1200.00
        ]);
    }

    /** @test */
    public function admin_can_create_asset_with_user_assignment()
    {
        $data = [
            'name' => 'Test Asset',
            'category' => 'Electronics',
            'purchase_date' => '2025-01-15',
            'purchase_price' => 1200.00,
            'assigned_to_user_id' => $this->adminUser->id
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/assets', $data);

        $response->assertStatus(201);
        $asset = Asset::latest()->first();
        $this->assertEquals($this->adminUser->id, $asset->assigned_to_user_id);
        $this->assertDatabaseHas('asset_movements', [
            'asset_id' => $asset->id,
            'movement_type' => 'assignment',
            'to_user_id' => $this->adminUser->id
        ]);
    }

    /** @test */
    public function admin_can_create_asset_with_location_assignment()
    {
        $data = [
            'name' => 'Test Asset',
            'category' => 'Furniture',
            'purchase_date' => '2025-01-15',
            'purchase_price' => 500.00,
            'assigned_to_location' => 'Room 101'
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/assets', $data);

        $response->assertStatus(201);
        $asset = Asset::latest()->first();
        $this->assertEquals('Room 101', $asset->assigned_to_location);
    }

    /** @test */
    public function asset_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->adminUser)->postJson('/api/assets', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'category', 'purchase_date', 'purchase_price']);
    }

    /** @test */
    public function asset_creation_validates_price_is_numeric()
    {
        $data = [
            'name' => 'Test Asset',
            'category' => 'Electronics',
            'purchase_date' => '2025-01-15',
            'purchase_price' => 'invalid'
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/assets', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['purchase_price']);
    }

    /** @test */
    public function asset_creation_generates_unique_code()
    {
        $data = [
            'name' => 'Test Asset',
            'category' => 'Electronics',
            'purchase_date' => '2025-01-15',
            'purchase_price' => 1200.00
        ];

        $response1 = $this->actingAs($this->adminUser)->postJson('/api/assets', $data);
        $response2 = $this->actingAs($this->adminUser)->postJson('/api/assets', $data);

        $response1->assertStatus(201);
        $response2->assertStatus(201);
        
        $asset1 = Asset::find($response1->json('data.asset.id'));
        $asset2 = Asset::find($response2->json('data.asset.id'));
        
        $this->assertNotEquals($asset1->asset_code, $asset2->asset_code);
        $this->assertStringStartsWith(strtoupper(substr($this->organization->slug, 0, 3)), $asset1->asset_code);
    }

    /** @test */
    public function admin_can_view_asset_details()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson("/api/assets/{$asset->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.asset.id', $asset->id);
    }

    /** @test */
    public function admin_cannot_view_other_organization_asset()
    {
        $asset = Asset::factory()->forOrganization($this->otherOrganization->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson("/api/assets/{$asset->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_asset()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create([
            'name' => 'Old Name',
            'status' => 'active'
        ]);

        $data = [
            'name' => 'Updated Name',
            'category' => $asset->category,
            'purchase_date' => $asset->purchase_date->format('Y-m-d'),
            'purchase_price' => $asset->purchase_price,
            'status' => 'in_repair',
            'status_change_reason' => 'Needs repair'
        ];

        $response = $this->actingAs($this->adminUser)->putJson("/api/assets/{$asset->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'name' => 'Updated Name',
            'status' => 'in_repair'
        ]);
    }

    /** @test */
    public function admin_can_delete_asset()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->adminUser)->deleteJson("/api/assets/{$asset->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('assets', ['id' => $asset->id]);
    }

    /** @test */
    public function admin_can_log_asset_movement()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();

        $data = [
            'movement_date' => now()->format('Y-m-d'),
            'movement_type' => 'assignment',
            'to_user_id' => $this->adminUser->id,
            'to_location' => null,
            'reason' => 'Assigned to user'
        ];

        $response = $this->actingAs($this->adminUser)->postJson("/api/assets/{$asset->id}/movement", $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('asset_movements', [
            'asset_id' => $asset->id,
            'movement_type' => 'assignment',
            'to_user_id' => $this->adminUser->id
        ]);
        $asset->refresh();
        $this->assertEquals($this->adminUser->id, $asset->assigned_to_user_id);
    }

    /** @test */
    public function asset_movement_updates_asset_assignment()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create([
            'assigned_to_user_id' => null
        ]);

        $data = [
            'movement_date' => now()->format('Y-m-d'),
            'movement_type' => 'assignment',
            'to_user_id' => $this->adminUser->id,
            'reason' => 'New assignment'
        ];

        $response = $this->actingAs($this->adminUser)->postJson("/api/assets/{$asset->id}/movement", $data);

        $response->assertStatus(201);
        $asset->refresh();
        $this->assertEquals($this->adminUser->id, $asset->assigned_to_user_id);
    }

    /** @test */
    public function asset_movement_validates_required_fields()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->adminUser)->postJson("/api/assets/{$asset->id}/movement", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['movement_date', 'movement_type']);
    }

    /** @test */
    public function admin_can_view_asset_with_maintenance_records()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();
        MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson("/api/assets/{$asset->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['asset' => ['maintenance_records']]]);
    }

    /** @test */
    public function finance_cannot_access_assets()
    {
        $response = $this->actingAs($this->financeUser)->getJson('/api/assets');

        $response->assertStatus(403);
    }

    /** @test */
    public function general_staff_cannot_create_asset()
    {
        $data = [
            'name' => 'Test Asset',
            'category' => 'Electronics',
            'purchase_date' => '2025-01-15',
            'purchase_price' => 1200.00
        ];

        $response = $this->actingAs($this->generalStaffUser)->postJson('/api/assets', $data);

        $response->assertStatus(403);
    }
}

