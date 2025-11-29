<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\MaintenanceRecord;
use Tests\Feature\TestCaseBase;

class MaintenanceControllerTest extends TestCaseBase
{
    /** @test */
    public function admin_can_view_maintenance_records()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();
        $otherAsset = Asset::factory()->forOrganization($this->otherOrganization->id)->create();
        $record1 = MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->create();
        $record2 = MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->create();
        $otherRecord = MaintenanceRecord::factory()->forOrganization($this->otherOrganization->id)->forAsset($otherAsset->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson('/api/maintenance');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['records', 'assets']]);
        $recordIds = collect($response->json('data.records.data'))->pluck('id')->toArray();
        $this->assertContains($record1->id, $recordIds);
        $this->assertContains($record2->id, $recordIds);
        $this->assertNotContains($otherRecord->id, $recordIds);
    }

    /** @test */
    public function admin_support_can_view_maintenance_records()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();
        MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->create();

        $response = $this->actingAs($this->adminSupportUser)->getJson('/api/maintenance');

        $response->assertStatus(200);
    }

    /** @test */
    public function maintenance_records_can_be_filtered_by_status()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();
        $pending = MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->pending()->create();
        $completed = MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->completed()->create();

        $response = $this->actingAs($this->adminUser)->getJson('/api/maintenance?status=pending');

        $response->assertStatus(200);
        $recordIds = collect($response->json('data.records.data'))->pluck('id')->toArray();
        $this->assertContains($pending->id, $recordIds);
        $this->assertNotContains($completed->id, $recordIds);
    }

    /** @test */
    public function maintenance_records_can_be_filtered_by_type()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();
        $scheduled = MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->create(['type' => 'scheduled']);
        $repair = MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->create(['type' => 'repair']);

        $response = $this->actingAs($this->adminUser)->getJson('/api/maintenance?type=scheduled');

        $response->assertStatus(200);
        $recordIds = collect($response->json('data.records.data'))->pluck('id')->toArray();
        $this->assertContains($scheduled->id, $recordIds);
        $this->assertNotContains($repair->id, $recordIds);
    }

    /** @test */
    public function admin_can_view_upcoming_maintenance()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();
        $upcoming = MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->pending()->create([
            'scheduled_date' => now()->addDays(3)
        ]);
        $past = MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->create([
            'scheduled_date' => now()->subDays(5)
        ]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/maintenance/upcoming');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['records']]);
    }

    /** @test */
    public function admin_can_create_maintenance_record()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();

        $data = [
            'asset_id' => $asset->id,
            'type' => 'scheduled',
            'scheduled_date' => now()->addDays(7)->format('Y-m-d'),
            'description' => 'Monthly maintenance check',
            'cost' => 150.00,
            'service_provider' => 'Tech Services Inc'
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/maintenance', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('maintenance_records', [
            'organization_id' => $this->organization->id,
            'asset_id' => $asset->id,
            'type' => 'scheduled',
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function maintenance_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->adminUser)->postJson('/api/maintenance', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['asset_id', 'type', 'scheduled_date', 'description']);
    }

    /** @test */
    public function admin_cannot_create_maintenance_for_other_organization_asset()
    {
        $asset = Asset::factory()->forOrganization($this->otherOrganization->id)->create();

        $data = [
            'asset_id' => $asset->id,
            'type' => 'scheduled',
            'scheduled_date' => now()->addDays(7)->format('Y-m-d'),
            'description' => 'Test'
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/maintenance', $data);

        $response->assertStatus(404);
    }

    /** @test */
    public function admin_can_view_maintenance_record_details()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();
        $record = MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson("/api/maintenance/{$record->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.record.id', $record->id);
    }

    /** @test */
    public function admin_can_update_maintenance_record()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();
        $record = MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->create([
            'status' => 'pending'
        ]);

        $data = [
            'asset_id' => $asset->id,
            'type' => $record->type,
            'scheduled_date' => $record->scheduled_date->format('Y-m-d'),
            'description' => 'Updated description',
            'status' => 'completed',
            'completed_date' => now()->format('Y-m-d'),
            'cost' => 200.00
        ];

        $response = $this->actingAs($this->adminUser)->putJson("/api/maintenance/{$record->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('maintenance_records', [
            'id' => $record->id,
            'status' => 'completed',
            'cost' => 200.00
        ]);
    }

    /** @test */
    public function admin_can_delete_maintenance_record()
    {
        $asset = Asset::factory()->forOrganization($this->organization->id)->create();
        $record = MaintenanceRecord::factory()->forOrganization($this->organization->id)->forAsset($asset->id)->create();

        $response = $this->actingAs($this->adminUser)->deleteJson("/api/maintenance/{$record->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('maintenance_records', ['id' => $record->id]);
    }

    /** @test */
    public function finance_cannot_access_maintenance()
    {
        $response = $this->actingAs($this->financeUser)->getJson('/api/maintenance');

        $response->assertStatus(403);
    }
}

