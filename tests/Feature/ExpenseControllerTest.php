<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\TestCaseBase;

class ExpenseControllerTest extends TestCaseBase
{
    use WithFaker;

    /** @test */
    public function admin_can_view_expenses()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        $expense1 = Expense::factory()->forOrganization($this->organization->id)->forCategory($category->id)->create();
        $expense2 = Expense::factory()->forOrganization($this->organization->id)->forCategory($category->id)->create();
        $otherExpense = Expense::factory()->forOrganization($this->otherOrganization->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson('/api/expenses');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['expenses', 'categories']]);
        $expenseIds = collect($response->json('data.expenses.data'))->pluck('id')->toArray();
        $this->assertContains($expense1->id, $expenseIds);
        $this->assertContains($expense2->id, $expenseIds);
        $this->assertNotContains($otherExpense->id, $expenseIds);
    }

    /** @test */
    public function finance_can_view_expenses()
    {
        $expense = Expense::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->financeUser)->getJson('/api/expenses');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['expenses']]);
    }

    /** @test */
    public function general_staff_can_view_expenses()
    {
        $expense = Expense::factory()->forOrganization($this->organization->id)->create();

        $response = $this->actingAs($this->generalStaffUser)->getJson('/api/view/expenses');

        $response->assertStatus(200);
    }

    /** @test */
    public function expenses_can_be_filtered_by_category()
    {
        $category1 = ExpenseCategory::factory()->forOrganization($this->organization->id)->create(['name' => 'Category 1']);
        $category2 = ExpenseCategory::factory()->forOrganization($this->organization->id)->create(['name' => 'Category 2']);
        $expense1 = Expense::factory()->forOrganization($this->organization->id)->forCategory($category1->id)->create();
        $expense2 = Expense::factory()->forOrganization($this->organization->id)->forCategory($category2->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson("/api/expenses?category_id={$category1->id}");

        $response->assertStatus(200);
        $expenseIds = collect($response->json('data.expenses.data'))->pluck('id')->toArray();
        $this->assertContains($expense1->id, $expenseIds);
        $this->assertNotContains($expense2->id, $expenseIds);
    }

    /** @test */
    public function expenses_can_be_filtered_by_date_range()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        $expense1 = Expense::factory()->forOrganization($this->organization->id)->forCategory($category->id)->create([
            'expense_date' => '2025-01-15'
        ]);
        $expense2 = Expense::factory()->forOrganization($this->organization->id)->forCategory($category->id)->create([
            'expense_date' => '2025-02-15'
        ]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/expenses?date_from=2025-01-01&date_to=2025-01-31');

        $response->assertStatus(200);
        $expenseIds = collect($response->json('data.expenses.data'))->pluck('id')->toArray();
        $this->assertContains($expense1->id, $expenseIds);
        $this->assertNotContains($expense2->id, $expenseIds);
    }

    /** @test */
    public function expenses_can_be_filtered_by_vendor()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        $expense1 = Expense::factory()->forOrganization($this->organization->id)->forCategory($category->id)->create([
            'vendor_payee' => 'Vendor A'
        ]);
        $expense2 = Expense::factory()->forOrganization($this->organization->id)->forCategory($category->id)->create([
            'vendor_payee' => 'Vendor B'
        ]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/expenses?vendor=Vendor A');

        $response->assertStatus(200);
        $expenseIds = collect($response->json('data.expenses.data'))->pluck('id')->toArray();
        $this->assertContains($expense1->id, $expenseIds);
        $this->assertNotContains($expense2->id, $expenseIds);
    }

    /** @test */
    public function admin_can_create_expense_with_existing_category()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();

        $data = [
            'expense_category_id' => $category->id,
            'expense_date' => '2025-01-15',
            'amount' => 150.50,
            'vendor_payee' => 'Test Vendor',
            'description' => 'Test expense'
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/expenses', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'data' => ['expense']]);
        $this->assertDatabaseHas('expenses', [
            'organization_id' => $this->organization->id,
            'expense_category_id' => $category->id,
            'amount' => 150.50,
            'vendor_payee' => 'Test Vendor'
        ]);
    }

    /** @test */
    public function admin_can_create_expense_with_new_category()
    {
        $data = [
            'category_type' => 'new',
            'category_name' => 'New Category',
            'expense_date' => '2025-01-15',
            'amount' => 200.00,
            'vendor_payee' => 'Test Vendor',
            'description' => 'Test expense'
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/expenses', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('expense_categories', [
            'organization_id' => $this->organization->id,
            'name' => 'New Category'
        ]);
        $category = ExpenseCategory::where('name', 'New Category')->first();
        $this->assertDatabaseHas('expenses', [
            'organization_id' => $this->organization->id,
            'expense_category_id' => $category->id,
            'amount' => 200.00
        ]);
    }

    /** @test */
    public function finance_can_create_expense()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();

        $data = [
            'expense_category_id' => $category->id,
            'expense_date' => '2025-01-15',
            'amount' => 100.00,
        ];

        $response = $this->actingAs($this->financeUser)->postJson('/api/expenses', $data);

        $response->assertStatus(201);
    }

    /** @test */
    public function expense_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->adminUser)->postJson('/api/expenses', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['expense_date', 'amount']);
    }

    /** @test */
    public function expense_creation_validates_amount_is_numeric()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();

        $data = [
            'expense_category_id' => $category->id,
            'expense_date' => '2025-01-15',
            'amount' => 'invalid',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/expenses', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function expense_can_be_created_with_receipt()
    {
        Storage::fake('public');
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        $file = UploadedFile::fake()->image('receipt.jpg');

        $data = [
            'expense_category_id' => $category->id,
            'expense_date' => '2025-01-15',
            'amount' => 150.00,
            'receipt' => $file
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/expenses', $data);

        $response->assertStatus(201);
        $expense = Expense::latest()->first();
        $this->assertNotNull($expense->receipt_path);
        Storage::disk('public')->assertExists($expense->receipt_path);
    }

    /** @test */
    public function admin_can_view_expense_details()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        $expense = Expense::factory()->forOrganization($this->organization->id)->forCategory($category->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson("/api/expenses/{$expense->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.expense.id', $expense->id);
    }

    /** @test */
    public function admin_cannot_view_other_organization_expense()
    {
        $expense = Expense::factory()->forOrganization($this->otherOrganization->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson("/api/expenses/{$expense->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_expense()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        $expense = Expense::factory()->forOrganization($this->organization->id)->forCategory($category->id)->create([
            'amount' => 100.00
        ]);

        $data = [
            'expense_category_id' => $category->id,
            'expense_date' => '2025-01-20',
            'amount' => 200.00,
            'vendor_payee' => 'Updated Vendor'
        ];

        $response = $this->actingAs($this->adminUser)->putJson("/api/expenses/{$expense->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 200.00,
            'vendor_payee' => 'Updated Vendor'
        ]);
    }

    /** @test */
    public function admin_can_update_expense_with_new_category()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        $expense = Expense::factory()->forOrganization($this->organization->id)->forCategory($category->id)->create();

        $data = [
            'category_type' => 'new',
            'category_name' => 'Updated Category',
            'expense_date' => $expense->expense_date->format('Y-m-d'),
            'amount' => $expense->amount
        ];

        $response = $this->actingAs($this->adminUser)->putJson("/api/expenses/{$expense->id}", $data);

        $response->assertStatus(200);
        $newCategory = ExpenseCategory::where('name', 'Updated Category')->first();
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'expense_category_id' => $newCategory->id
        ]);
    }

    /** @test */
    public function admin_can_delete_expense()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        $expense = Expense::factory()->forOrganization($this->organization->id)->forCategory($category->id)->create();

        $response = $this->actingAs($this->adminUser)->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
    }

    /** @test */
    public function admin_can_delete_expense_with_receipt()
    {
        Storage::fake('public');
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        $file = UploadedFile::fake()->image('receipt.jpg');
        $expense = Expense::factory()->forOrganization($this->organization->id)->forCategory($category->id)->create([
            'receipt_path' => 'expenses/receipts/test.jpg'
        ]);
        Storage::disk('public')->put($expense->receipt_path, $file->getContent());

        $response = $this->actingAs($this->adminUser)->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(200);
        Storage::disk('public')->assertMissing($expense->receipt_path);
    }

    /** @test */
    public function admin_can_view_expense_reports()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        Expense::factory()->count(5)->forOrganization($this->organization->id)->forCategory($category->id)->create([
            'expense_date' => now()->subMonth(),
            'amount' => 100
        ]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/expenses/reports');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['period', 'total_amount', 'category_totals']]);
    }

    /** @test */
    public function admin_can_view_expense_charts()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        Expense::factory()->count(3)->forOrganization($this->organization->id)->forCategory($category->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson('/api/expenses/charts');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['bar_chart', 'line_chart', 'pie_chart']]);
    }

    /** @test */
    public function admin_can_export_expenses()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();
        Expense::factory()->count(3)->forOrganization($this->organization->id)->forCategory($category->id)->create();

        $response = $this->actingAs($this->adminUser)->getJson('/api/expenses/export?format=excel');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['expenses', 'format']]);
    }

    /** @test */
    public function general_staff_cannot_create_expense()
    {
        $category = ExpenseCategory::factory()->forOrganization($this->organization->id)->create();

        $data = [
            'expense_category_id' => $category->id,
            'expense_date' => '2025-01-15',
            'amount' => 100.00,
        ];

        $response = $this->actingAs($this->generalStaffUser)->postJson('/api/expenses', $data);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_support_cannot_access_expenses()
    {
        $response = $this->actingAs($this->adminSupportUser)->getJson('/api/expenses');

        $response->assertStatus(403);
    }
}

