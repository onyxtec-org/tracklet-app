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
     *     @OA\Response(
     *         response=200,
     *         description="List of expense categories for the organization",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="categories", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Office Supplies"),
     *                         @OA\Property(property="description", type="string", nullable=true, example="Office related expenses"),
     *                         @OA\Property(property="is_system", type="boolean", example=false),
     *                         @OA\Property(property="organization_id", type="integer", example=1)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized - User does not belong to an organization")
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
     *             @OA\Property(property="name", type="string", example="Office Supplies", description="Category name (must be unique within organization)"),
     *             @OA\Property(property="description", type="string", nullable=true, example="Office related expenses", description="Optional category description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expense category created successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="category", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Office Supplies"),
     *                     @OA\Property(property="description", type="string", nullable=true, example="Office related expenses"),
     *                     @OA\Property(property="is_system", type="boolean", example=false),
     *                     @OA\Property(property="organization_id", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error - Category name already exists or validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The name has already been taken."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="The name has already been taken.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized - User does not belong to an organization")
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
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), description="Category ID"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Office Supplies", description="Category name (must be unique within organization)"),
     *             @OA\Property(property="description", type="string", nullable=true, example="Office related expenses", description="Optional category description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expense category updated successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="category", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Office Supplies"),
     *                     @OA\Property(property="description", type="string", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized - System categories cannot be modified or user does not belong to organization"),
     *     @OA\Response(response=422, description="Validation error - Category name already exists")
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
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), description="Category ID"),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expense category deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Cannot delete category with existing expenses",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cannot delete category with existing expenses.")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized - System categories cannot be deleted or user does not belong to organization")
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
