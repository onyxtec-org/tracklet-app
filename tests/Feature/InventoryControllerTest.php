<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventoryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $organization;
    protected $adminUser;
    protected $adminSupportUser;
    protected $superAdmin;
    protected $otherOrganization;
    protected $otherOrgUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'admin_support', 'guard_name' => 'web']);
        Role::create(['name' => 'finance', 'guard_name' => 'web']);

        // Create organizations
        $this->organization = Organization::factory()->onTrial()->create();
        $this->otherOrganization = Organization::factory()->onTrial()->create();

        // Create users
        $this->adminUser = User::factory()->forOrganization($this->organization->id)->create();
        $this->adminUser->assignRole('admin');

        $this->adminSupportUser = User::factory()->forOrganization($this->organization->id)->create();
        $this->adminSupportUser->assignRole('admin_support');

        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->otherOrgUser = User::factory()->forOrganization($this->otherOrganization->id)->create();
        $this->otherOrgUser->assignRole('admin');
    }

    /** @test */
    public function super_admin_cannot_access_inventory_index()
    {
        $response = $this->actingAs($this->superAdmin)->getJson('/api/inventory/items');

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Super Admin cannot access inventory management. Please use an organization account.'
        ]);
    }

    /** @test */
    public function admin_can_view_inventory_items()
    {
        $item1 = InventoryItem::factory()->forOrganization($this->organization->id)->create();
        $item2 = InventoryItem::factory()->forOrganization($this->organization->id)->create();
        $otherItem = InventoryItem::factory()->forOrganization($this->otherOrganization->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson('/api/inventory/items');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['items']]);
        $itemNames = collect($response->json('data.items.data'))->pluck('name')->toArray();
        $this->assertContains($item1->name, $itemNames);
        $this->assertContains($item2->name, $itemNames);
        $this->assertNotContains($otherItem->name, $itemNames); // Should not see other org's items
    }

    /** @test */
    public function admin_support_can_view_inventory_items()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->adminSupportUser)->getJson('/api/inventory/items');

        $response->assertStatus(200);
        $itemNames = collect($response->json('data.items.data'))->pluck('name')->toArray();
        $this->assertContains($item->name, $itemNames);
    }

    /** @test */
    public function inventory_index_filters_by_category()
    {
        $item1 = InventoryItem::factory()->forOrganization($this->organization->id)->create(['category' => 'Office Supplies']);
        $item2 = InventoryItem::factory()->forOrganization($this->organization->id)->create(['category' => 'Electronics']);

        $response = $this->actingAs($this->adminUser)->getJson('/api/inventory/items?category=Office Supplies');

        $response->assertStatus(200);
        $itemNames = collect($response->json('data.items.data'))->pluck('name')->toArray();
        $this->assertContains($item1->name, $itemNames);
        $this->assertNotContains($item2->name, $itemNames);
    }

    /** @test */
    public function inventory_index_filters_low_stock_items()
    {
        $item1 = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'quantity' => 5,
            'minimum_threshold' => 20,
        ]);
        $item2 = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'quantity' => 50,
            'minimum_threshold' => 20,
        ]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/inventory/items?low_stock=1');

        $response->assertStatus(200);
        $itemNames = collect($response->json('data.items.data'))->pluck('name')->toArray();
        $this->assertContains($item1->name, $itemNames);
        $this->assertNotContains($item2->name, $itemNames);
    }

    /** @test */
    public function admin_can_create_inventory_item()
    {
        $data = [
            'name' => 'Test Item',
            'category' => 'Office Supplies',
            'quantity' => 100,
            'minimum_threshold' => 20,
            'unit_price' => 5.50,
            'unit' => 'pieces',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/inventory/items', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('inventory_items', [
            'name' => 'Test Item',
            'organization_id' => $this->organization->id,
            'quantity' => 100,
            'total_price' => 550.00, // 100 * 5.50
        ]);
    }

    /** @test */
    public function super_admin_cannot_create_inventory_item()
    {
        $data = [
            'name' => 'Test Item',
            'quantity' => 100,
            'minimum_threshold' => 20,
            'unit_price' => 5.50,
        ];

        $response = $this->actingAs($this->superAdmin)->postJson('/api/inventory/items', $data);

        $response->assertStatus(403);
    }

    /** @test */
    public function inventory_item_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->adminUser)->postJson('/api/inventory/items', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'quantity', 'minimum_threshold', 'unit_price']);
    }

    /** @test */
    public function admin_can_view_inventory_item()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson("/api/inventory/items/{$item->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals($item->name, $response->json('data.item.name'));
    }

    /** @test */
    public function super_admin_cannot_view_inventory_item()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->superAdmin)->getJson("/api/inventory/items/{$item->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_view_other_organization_item()
    {
        $item = InventoryItem::factory()->forOrganization($this->otherOrganization->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson("/api/inventory/items/{$item->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_inventory_item()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'name' => 'Original Name',
            'quantity' => 50,
        ]);

        $data = [
            'name' => 'Updated Name',
            'category' => 'Electronics',
            'quantity' => 75,
            'minimum_threshold' => 25,
            'unit_price' => 10.00,
            'unit' => 'boxes',
        ];

        $response = $this->actingAs($this->adminUser)->putJson("/api/inventory/items/{$item->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('inventory_items', [
            'id' => $item->id,
            'name' => 'Updated Name',
            'quantity' => 75,
            'total_price' => 750.00, // 75 * 10.00
        ]);
    }

    /** @test */
    public function super_admin_cannot_update_inventory_item()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->superAdmin)->putJson("/api/inventory/items/{$item->id}", [
            'name' => 'Updated',
            'quantity' => 100,
            'minimum_threshold' => 20,
            'unit_price' => 5.00,
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_inventory_item()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->adminUser)->deleteJson("/api/inventory/items/{$item->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('inventory_items', ['id' => $item->id]);
    }

    /** @test */
    public function super_admin_cannot_delete_inventory_item()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->superAdmin)->deleteJson("/api/inventory/items/{$item->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_log_stock_in_transaction()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'quantity' => 50,
            'unit_price' => 5.00,
        ]);

        $data = [
            'type' => 'in',
            'quantity' => 30,
            'transaction_date' => now()->format('Y-m-d'),
            'reference' => 'PO-12345',
            'notes' => 'New purchase',
            'unit_price' => 5.50,
            'vendor' => 'Supplier Co',
        ];

        $response = $this->actingAs($this->adminUser)->postJson("/api/inventory/items/{$item->id}/stock", $data);

        $response->assertStatus(201);
        
        // Check quantity increased
        $item->refresh();
        $this->assertEquals(80, $item->quantity); // 50 + 30
        $this->assertEquals(5.50, $item->unit_price); // Updated unit price
        $this->assertEquals(440.00, $item->total_price); // 80 * 5.50

        // Check transaction created
        $this->assertDatabaseHas('stock_transactions', [
            'inventory_item_id' => $item->id,
            'type' => 'in',
            'quantity' => 30,
            'user_id' => $this->adminUser->id,
        ]);
    }

    /** @test */
    public function admin_can_log_stock_out_transaction()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'quantity' => 50,
            'unit_price' => 5.00,
        ]);

        $data = [
            'type' => 'out',
            'quantity' => 20,
            'transaction_date' => now()->format('Y-m-d'),
            'reference' => 'Used for office',
            'notes' => 'Distributed to departments',
        ];

        $response = $this->actingAs($this->adminUser)->postJson("/api/inventory/items/{$item->id}/stock", $data);

        $response->assertStatus(201);
        
        // Check quantity decreased
        $item->refresh();
        $this->assertEquals(30, $item->quantity); // 50 - 20
        $this->assertEquals(150.00, $item->total_price); // 30 * 5.00

        // Check transaction created
        $this->assertDatabaseHas('stock_transactions', [
            'inventory_item_id' => $item->id,
            'type' => 'out',
            'quantity' => 20,
        ]);
    }

    /** @test */
    public function stock_out_fails_when_insufficient_stock()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'quantity' => 10,
        ]);

        $data = [
            'type' => 'out',
            'quantity' => 25, // More than available
            'transaction_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->adminUser)->postJson("/api/inventory/items/{$item->id}/stock", $data);

        $response->assertStatus(400);
        $response->assertJsonFragment(['message' => 'Failed to log stock transaction: Insufficient stock']);
        
        // Quantity should not change
        $item->refresh();
        $this->assertEquals(10, $item->quantity);
    }

    /** @test */
    public function super_admin_cannot_log_stock_transaction()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->superAdmin)->postJson("/api/inventory/items/{$item->id}/stock", [
            'type' => 'in',
            'quantity' => 10,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function stock_transaction_validates_required_fields()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->adminUser)->postJson("/api/inventory/items/{$item->id}/stock", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type', 'quantity', 'transaction_date']);
    }

    /** @test */
    public function stock_transaction_validates_type_enum()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->adminUser)->postJson("/api/inventory/items/{$item->id}/stock", [
            'type' => 'invalid',
            'quantity' => 10,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type']);
    }

    /** @test */
    public function multiple_stock_transactions_update_quantity_correctly()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'quantity' => 100,
        ]);

        // Stock in 50
        $this->actingAs($this->adminUser)->postJson("/api/inventory/items/{$item->id}/stock", [
            'type' => 'in',
            'quantity' => 50,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $item->refresh();
        $this->assertEquals(150, $item->quantity);

        // Stock out 30
        $this->actingAs($this->adminUser)->postJson("/api/inventory/items/{$item->id}/stock", [
            'type' => 'out',
            'quantity' => 30,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $item->refresh();
        $this->assertEquals(120, $item->quantity);

        // Stock in 20
        $this->actingAs($this->adminUser)->postJson("/api/inventory/items/{$item->id}/stock", [
            'type' => 'in',
            'quantity' => 20,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $item->refresh();
        $this->assertEquals(140, $item->quantity);
    }

    /** @test */
    public function admin_can_view_low_stock_items()
    {
        $lowStockItem = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'quantity' => 5,
            'minimum_threshold' => 20,
        ]);
        $normalItem = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'quantity' => 50,
            'minimum_threshold' => 20,
        ]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/inventory/low-stock');

        $response->assertStatus(200);
        $itemNames = collect($response->json('data.items'))->pluck('name')->toArray();
        $this->assertContains($lowStockItem->name, $itemNames);
        $this->assertNotContains($normalItem->name, $itemNames);
    }

    /** @test */
    public function admin_can_view_purchase_history()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create();
        
        StockTransaction::factory()->stockIn()->create([
            'organization_id' => $this->organization->id,
            'inventory_item_id' => $item->id,
            'user_id' => $this->adminUser->id,
        ]);

        StockTransaction::factory()->stockOut()->create([
            'organization_id' => $this->organization->id,
            'inventory_item_id' => $item->id,
            'user_id' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/inventory/purchase-history');

        $response->assertStatus(200);
        // Should only show stock in transactions
        $this->assertEquals(1, $response->json('data.transactions.data') ? count($response->json('data.transactions.data')) : 0);
    }

    /** @test */
    public function admin_can_view_aging_report()
    {
        $item1 = InventoryItem::factory()->forOrganization($this->organization->id)->create();
        $item2 = InventoryItem::factory()->forOrganization($this->organization->id)->create();

        // Create old stock in transaction for item1
        StockTransaction::factory()->stockIn()->create([
            'organization_id' => $this->organization->id,
            'inventory_item_id' => $item1->id,
            'transaction_date' => now()->subMonths(6),
        ]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/inventory/aging-report');

        $response->assertStatus(200);
        $itemNames = collect($response->json('data.items'))->pluck('name')->toArray();
        $this->assertContains($item1->name, $itemNames);
    }

    /** @test */
    public function api_endpoints_work_with_sanctum_authentication()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create();

        // Get API token
        $token = $this->adminUser->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/inventory/items');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'items' => [
                    'data' => [
                        '*' => ['id', 'name', 'quantity', 'unit_price']
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function api_stock_transaction_updates_quantity()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'quantity' => 50,
        ]);

        $token = $this->adminUser->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/inventory/items/{$item->id}/stock", [
                'type' => 'in',
                'quantity' => 25,
                'transaction_date' => now()->format('Y-m-d'),
            ]);

        $response->assertStatus(201);
        $item->refresh();
        $this->assertEquals(75, $item->quantity);
    }

    /** @test */
    public function stock_in_updates_unit_price_when_provided()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'quantity' => 50,
            'unit_price' => 5.00,
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/api/inventory/items/{$item->id}/stock", [
            'type' => 'in',
            'quantity' => 10,
            'transaction_date' => now()->format('Y-m-d'),
            'unit_price' => 6.00,
        ]);

        $response->assertStatus(201);
        $item->refresh();
        $this->assertEquals(6.00, $item->unit_price);
    }

    /** @test */
    public function stock_in_does_not_update_unit_price_when_not_provided()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'quantity' => 50,
            'unit_price' => 5.00,
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/api/inventory/items/{$item->id}/stock", [
            'type' => 'in',
            'quantity' => 10,
            'transaction_date' => now()->format('Y-m-d'),
            // unit_price not provided
        ]);

        $response->assertStatus(201);
        $item->refresh();
        $this->assertEquals(5.00, $item->unit_price); // Should remain unchanged
    }

    /** @test */
    public function total_price_recalculates_after_stock_transaction()
    {
        $item = InventoryItem::factory()->forOrganization($this->organization->id)->create([
            'quantity' => 50,
            'unit_price' => 5.00,
            'total_price' => 250.00,
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/api/inventory/items/{$item->id}/stock", [
            'type' => 'in',
            'quantity' => 30,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(201);
        $item->refresh();
        $this->assertEquals(80, $item->quantity);
        $this->assertEquals(400.00, $item->total_price); // 80 * 5.00
    }
}

