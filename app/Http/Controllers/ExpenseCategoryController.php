<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/expenses/categories",
     *     summary="List expense categories",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="List of categories")
     * )
     */
    public function index()
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $categories = ExpenseCategory::where('organization_id', $organization->id)
            ->orderBy('name')
            ->get();

        return $this->respond(
            ['categories' => $categories],
            'expenses.categories.index',
            ['categories' => $categories]
        );
    }

    /**
     * @OA\Post(
     *     path="/api/expenses/categories",
     *     summary="Create expense category",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Office Supplies"),
     *             @OA\Property(property="description", type="string", example="Office related expenses")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Category created successfully")
     * )
     */
    public function store(Request $request)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,NULL,id,organization_id,' . $organization->id,
            'description' => 'nullable|string',
        ]);

        $category = ExpenseCategory::create([
            'organization_id' => $organization->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        return $this->respond([
            'message' => 'Expense category created successfully.',
            'category' => $category,
        ], null, [], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/expenses/categories/{id}",
     *     summary="Update expense category",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Category updated successfully")
     * )
     */
    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization || $expenseCategory->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        if ($expenseCategory->is_system) {
            return $this->respondError('System categories cannot be modified.', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id . ',id,organization_id,' . $organization->id,
            'description' => 'nullable|string',
        ]);

        $expenseCategory->update($validated);

        return $this->respond([
            'message' => 'Expense category updated successfully.',
            'category' => $expenseCategory->fresh(),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/expenses/categories/{id}",
     *     summary="Delete expense category",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category deleted successfully")
     * )
     */
    public function destroy(ExpenseCategory $expenseCategory)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization || $expenseCategory->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        if ($expenseCategory->is_system) {
            return $this->respondError('System categories cannot be deleted.', 403);
        }

        // Check if category has expenses
        if ($expenseCategory->expenses()->count() > 0) {
            return $this->respondError('Cannot delete category with existing expenses.', 400);
        }

        $expenseCategory->delete();

        return $this->respond([
            'message' => 'Expense category deleted successfully.',
        ]);
    }
}
