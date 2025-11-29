<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Tests\Feature\TestCaseBase;

class ExpenseCategoryControllerTest extends TestCaseBase
{
    /** @test */
    public function admin_can_view_categories()
    {
        $category1 = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        $category2 = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        $otherCategory = ExpenseCategory::factory()->forOrganization($this->otherOrganization->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson('/api/expenses/categories');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['categories']]);
        $categoryIds = collect($response->json('data.categories'))->pluck('id')->toArray();
        $this->assertContains($category1->id, $categoryIds);
        $this->assertContains($category2->id, $categoryIds);
        $this->assertNotContains($otherCategory->id, $categoryIds);
    }

    /** @test */
    public function finance_can_view_categories()
    {
        ExpenseCategory::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->financeUser)->getJson('/api/expenses/categories');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_category()
    {
        $data = [
            'name' => 'New Category',
            'description' => 'Category description'
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/expenses/categories', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('expense_categories', [
            'organization_id' => $this->organization->id,
            'name' => 'New Category',
            'description' => 'Category description',
            'is_system' => false
        ]);
    }

    /** @test */
    public function category_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->adminUser)->postJson('/api/expenses/categories', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function category_name_must_be_unique_per_organization()
    {
        ExpenseCategory::factory()->forOrganization($this->organization->id)->create(['name' => 'Existing Category']);

        $data = ['name' => 'Existing Category'];

        $response = $this->actingAs($this->adminUser)->postJson('/api/expenses/categories', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function different_organizations_can_have_same_category_name()
    {
        ExpenseCategory::factory()->forOrganization($this->otherOrganization->id)->create(['name' => 'Shared Category']);

        $data = ['name' => 'Shared Category'];

        $response = $this->actingAs($this->adminUser)->postJson('/api/expenses/categories', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('expense_categories', [
            'organization_id' => $this->organization->id,
            'name' => 'Shared Category'
        ]);
    }

    /** @test */
    public function admin_can_update_category()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create([
            'name' => 'Old Name',
            'is_system' => false
        ]);

        $data = [
            'name' => 'Updated Name',
            'description' => 'Updated description'
        ];

        $response = $this->actingAs($this->adminUser)->putJson("/api/expenses/categories/{$category->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'description' => 'Updated description'
        ]);
    }

    /** @test */
    public function system_category_cannot_be_updated()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create([
            'is_system' => true
        ]);

        $data = ['name' => 'Updated Name'];

        $response = $this->actingAs($this->adminUser)->putJson("/api/expenses/categories/{$category->id}", $data);

        $response->assertStatus(403);
        $response->assertJsonPath('message', 'System categories cannot be modified.');
    }

    /** @test */
    public function admin_can_delete_category()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create([
            'is_system' => false
        ]);

        $response = $this->actingAs($this->adminUser)->deleteJson("/api/expenses/categories/{$category->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('expense_categories', ['id' => $category->id]);
    }

    /** @test */
    public function category_with_expenses_cannot_be_deleted()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create([
            'is_system' => false
        ]);
        Expense::factory()->forOrganization($this->organization->id)->forCategory($category->id)->create();

        $response = $this->actingAs($this->adminUser)->deleteJson("/api/expenses/categories/{$category->id}");

        $response->assertStatus(400);
        $response->assertJsonPath('message', 'Cannot delete category with existing expenses.');
    }

    /** @test */
    public function system_category_cannot_be_deleted()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create([
            'is_system' => true
        ]);

        $response = $this->actingAs($this->adminUser)->deleteJson("/api/expenses/categories/{$category->id}");

        $response->assertStatus(403);
        $response->assertJsonPath('message', 'System categories cannot be deleted.');
    }

    /** @test */
    public function admin_cannot_access_other_organization_category()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->otherOrganization->id)->create();

        $response = $this->actingAs($this->adminUser)->putJson("/api/expenses/categories/{$category->id}", [
            'name' => 'Updated'
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function finance_can_create_category()
    {
        $data = ['name' => 'Finance Category'];

        $response = $this->actingAs($this->financeUser)->postJson('/api/expenses/categories', $data);

        $response->assertStatus(201);
    }
}

