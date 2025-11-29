<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Organization;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/expenses",
     *     summary="List expenses",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="date_from", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="date_to", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="vendor", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of expenses")
     * )
     */
    public function index(Request $request)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $query = Expense::where('organization_id', $organization->id)
            ->with(['category', 'user'])
            ->orderBy('expense_date', 'desc');

        // Apply filters
        if ($request->has('category_id') && $request->category_id) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        if ($request->has('vendor') && $request->vendor) {
            $query->where('vendor_payee', 'like', '%' . $request->vendor . '%');
        }

        $expenses = $query->paginate(20);

        $categories = ExpenseCategory::where('organization_id', $organization->id)
            ->orderBy('name')
            ->get();

        return $this->respond(
            [
                'expenses' => $expenses,
                'categories' => $categories,
                'filters' => $request->only(['category_id', 'date_from', 'date_to', 'vendor']),
            ],
            'expenses.index',
            [
                'expenses' => $expenses,
                'categories' => $categories,
                'filters' => $request->only(['category_id', 'date_from', 'date_to', 'vendor']),
            ]
        );
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
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
            'expenses.create',
            ['categories' => $categories]
        );
    }

    /**
     * @OA\Post(
     *     path="/api/expenses",
     *     summary="Create expense",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"expense_date", "amount"},
     *             @OA\Property(property="expense_category_id", type="integer", example=1),
     *             @OA\Property(property="category_name", type="string", example="New Category"),
     *             @OA\Property(property="expense_date", type="string", format="date", example="2025-11-28"),
     *             @OA\Property(property="amount", type="number", format="float", example="150.00"),
     *             @OA\Property(property="vendor_payee", type="string", example="Office Depot"),
     *             @OA\Property(property="description", type="string", example="Office supplies")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Expense created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        // Handle category_type from form (web interface)
        $categoryType = $request->input('category_type', 'existing');
        if ($categoryType === 'new') {
            // If creating new category, clear category_id
            $request->merge(['expense_category_id' => null]);
        } else {
            // If selecting existing, clear category_name
            $request->merge(['category_name' => null]);
        }

        $validated = $request->validate([
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'category_name' => 'nullable|string|max:255',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'vendor_payee' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        ], [
            'expense_category_id.required_without' => 'Please select a category or create a new one.',
            'category_name.required_without' => 'Please enter a category name or select an existing category.',
        ]);

        // Validate that at least one category identifier is provided
        if (empty($validated['expense_category_id']) && empty($validated['category_name'])) {
            return $this->respondError('Either expense_category_id or category_name is required.', 422);
        }

        // If both are provided, prioritize category_id (existing category takes precedence)
        $categoryId = !empty($validated['expense_category_id']) ? $validated['expense_category_id'] : null;
        $categoryName = !empty($validated['category_name']) ? $validated['category_name'] : null;

        // Get or create category
        $category = $this->getOrCreateCategory($organization, $categoryId, $categoryName);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store(
                'expenses/receipts/' . $organization->id,
                'public'
            );
        }

        $expense = Expense::create([
            'organization_id' => $organization->id,
            'expense_category_id' => $category->id,
            'user_id' => auth()->id(),
            'expense_date' => $validated['expense_date'],
            'amount' => $validated['amount'],
            'vendor_payee' => $validated['vendor_payee'] ?? null,
            'description' => $validated['description'] ?? null,
            'receipt_path' => $receiptPath,
        ]);

        return $this->respond([
            'message' => 'Expense created successfully.',
            'expense' => $expense->load(['category', 'user']),
            'redirect' => route('expenses.index'),
        ], null, [], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/expenses/{id}",
     *     summary="Get expense",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Expense details"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function show(Expense $expense)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization || $expense->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        $expense->load(['category', 'user']);

        return $this->respond(
            ['expense' => $expense],
            'expenses.show',
            ['expense' => $expense]
        );
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization || $expense->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        $categories = ExpenseCategory::where('organization_id', $organization->id)
            ->orderBy('name')
            ->get();

        return $this->respond(
            [
                'expense' => $expense->load(['category']),
                'categories' => $categories,
            ],
            'expenses.edit',
            [
                'expense' => $expense->load(['category']),
                'categories' => $categories,
            ]
        );
    }

    /**
     * @OA\Put(
     *     path="/api/expenses/{id}",
     *     summary="Update expense",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"expense_date", "amount"},
     *             @OA\Property(property="expense_category_id", type="integer"),
     *             @OA\Property(property="category_name", type="string"),
     *             @OA\Property(property="expense_date", type="string", format="date"),
     *             @OA\Property(property="amount", type="number", format="float"),
     *             @OA\Property(property="vendor_payee", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Expense updated successfully"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function update(Request $request, Expense $expense)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization || $expense->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        // Handle category_type from form (web interface)
        $categoryType = $request->input('category_type', 'existing');
        if ($categoryType === 'new') {
            // If creating new category, clear category_id
            $request->merge(['expense_category_id' => null]);
        } else {
            // If selecting existing, clear category_name
            $request->merge(['category_name' => null]);
        }

        $validated = $request->validate([
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'category_name' => 'nullable|string|max:255',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'vendor_payee' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'expense_category_id.required_without' => 'Please select a category or create a new one.',
            'category_name.required_without' => 'Please enter a category name or select an existing category.',
        ]);

        // Validate that at least one category identifier is provided
        if (empty($validated['expense_category_id']) && empty($validated['category_name'])) {
            return $this->respondError('Either expense_category_id or category_name is required.', 422);
        }

        // If both are provided, prioritize category_id (existing category takes precedence)
        $categoryId = !empty($validated['expense_category_id']) ? $validated['expense_category_id'] : null;
        $categoryName = !empty($validated['category_name']) ? $validated['category_name'] : null;

        // Get or create category
        $category = $this->getOrCreateCategory($organization, $categoryId, $categoryName);

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            
            $receiptPath = $request->file('receipt')->store(
                'expenses/receipts/' . $organization->id,
                'public'
            );
            $validated['receipt_path'] = $receiptPath;
        }

        $expense->update([
            'expense_category_id' => $category->id,
            'expense_date' => $validated['expense_date'],
            'amount' => $validated['amount'],
            'vendor_payee' => $validated['vendor_payee'] ?? null,
            'description' => $validated['description'] ?? null,
            'receipt_path' => $validated['receipt_path'] ?? $expense->receipt_path,
        ]);

        return $this->respond([
            'message' => 'Expense updated successfully.',
            'expense' => $expense->fresh()->load(['category', 'user']),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/expenses/{id}",
     *     summary="Delete expense",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Expense deleted successfully"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function destroy(Expense $expense)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization || $expense->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        // Delete receipt file if exists
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return $this->respond([
            'message' => 'Expense deleted successfully.',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/expenses/reports",
     *     summary="Get expense reports",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="period", in="query", @OA\Schema(type="string", enum={"monthly", "quarterly", "ytd"})),
     *     @OA\Parameter(name="year", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="month", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="quarter", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Expense reports")
     * )
     */
    public function reports(Request $request)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $period = $request->get('period', 'monthly'); // monthly, quarterly, ytd
        $year = $request->get('year', date('Y'));

        $query = Expense::where('organization_id', $organization->id);

        switch ($period) {
            case 'monthly':
                $month = $request->get('month', date('m'));
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                $query->whereBetween('expense_date', [$startDate, $endDate]);
                break;

            case 'quarterly':
                $quarter = $request->get('quarter', ceil(date('m') / 3));
                $startMonth = ($quarter - 1) * 3 + 1;
                $startDate = Carbon::create($year, $startMonth, 1)->startOfMonth();
                $endDate = $startDate->copy()->addMonths(2)->endOfMonth();
                $query->whereBetween('expense_date', [$startDate, $endDate]);
                break;

            case 'ytd':
                $startDate = Carbon::create($year, 1, 1)->startOfYear();
                $endDate = Carbon::now();
                $query->whereBetween('expense_date', [$startDate, $endDate]);
                break;
        }

        $expenses = $query->with('category')->get();

        // Calculate totals by category
        $categoryTotals = $expenses->groupBy('expense_category_id')
            ->map(function ($items) {
                return [
                    'category' => $items->first()->category->name,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $totalAmount = $expenses->sum('amount');
        $totalCount = $expenses->count();

        return $this->respond([
            'period' => $period,
            'year' => $year,
            'total_amount' => $totalAmount,
            'total_count' => $totalCount,
            'category_totals' => $categoryTotals,
            'expenses' => $expenses,
        ], 'expenses.reports', [
            'period' => $period,
            'year' => $year,
            'total_amount' => $totalAmount,
            'total_count' => $totalCount,
            'category_totals' => $categoryTotals,
            'expenses' => $expenses,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/expenses/charts",
     *     summary="Get expense charts data",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="period", in="query", @OA\Schema(type="string", enum={"monthly", "quarterly", "ytd"})),
     *     @OA\Parameter(name="year", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Chart data (bar, line, pie)")
     * )
     */
    public function charts(Request $request)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $period = $request->get('period', 'monthly');
        $year = $request->get('year', date('Y'));

        $query = Expense::where('organization_id', $organization->id);

        switch ($period) {
            case 'monthly':
                $month = $request->get('month', date('m'));
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                $query->whereBetween('expense_date', [$startDate, $endDate]);
                break;

            case 'quarterly':
                $quarter = $request->get('quarter', ceil(date('m') / 3));
                $startMonth = ($quarter - 1) * 3 + 1;
                $startDate = Carbon::create($year, $startMonth, 1)->startOfMonth();
                $endDate = $startDate->copy()->addMonths(2)->endOfMonth();
                $query->whereBetween('expense_date', [$startDate, $endDate]);
                break;

            case 'ytd':
                $startDate = Carbon::create($year, 1, 1)->startOfYear();
                $endDate = Carbon::now();
                $query->whereBetween('expense_date', [$startDate, $endDate]);
                break;
        }

        $expenses = $query->with('category')->get();

        // Bar Chart Data (Category breakdown)
        $barChartData = $expenses->groupBy('expense_category_id')
            ->map(function ($items, $categoryId) {
                return [
                    'category' => $items->first()->category->name,
                    'amount' => $items->sum('amount'),
                ];
            })
            ->sortByDesc('amount')
            ->values();

        // Line Chart Data (Trend over time)
        $lineChartData = $expenses->groupBy(function ($expense) {
            return $expense->expense_date->format('Y-m-d');
        })->map(function ($items, $date) {
            return [
                'date' => $date,
                'amount' => $items->sum('amount'),
            ];
        })->sortBy('date')->values();

        // Pie Chart Data (Category percentage)
        $totalAmount = $expenses->sum('amount');
        $pieChartData = $expenses->groupBy('expense_category_id')
            ->map(function ($items) use ($totalAmount) {
                $amount = $items->sum('amount');
                return [
                    'category' => $items->first()->category->name,
                    'amount' => $amount,
                    'percentage' => $totalAmount > 0 ? round(($amount / $totalAmount) * 100, 2) : 0,
                ];
            })
            ->sortByDesc('amount')
            ->values();

        return $this->respond([
            'bar_chart' => $barChartData,
            'line_chart' => $lineChartData,
            'pie_chart' => $pieChartData,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/expenses/export",
     *     summary="Export expenses",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="format", in="query", @OA\Schema(type="string", enum={"excel", "pdf"})),
     *     @OA\Response(response=200, description="Export data")
     * )
     */
    public function export(Request $request)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $format = $request->get('format', 'excel'); // excel or pdf
        $query = Expense::where('organization_id', $organization->id)
            ->with(['category', 'user']);

        // Apply same filters as index
        if ($request->has('category_id') && $request->category_id) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        // For now, return JSON. In production, use Laravel Excel or DomPDF
        // This is a placeholder - you'll need to install maatwebsite/excel or barryvdh/laravel-dompdf
        return $this->respond([
            'message' => 'Export functionality requires Laravel Excel or DomPDF package.',
            'expenses' => $expenses,
            'format' => $format,
        ]);
    }

    /**
     * Get or create expense category
     * 
     * @param Organization $organization
     * @param int|null $categoryId
     * @param string|null $categoryName
     * @return ExpenseCategory
     */
    private function getOrCreateCategory(Organization $organization, ?int $categoryId = null, ?string $categoryName = null): ExpenseCategory
    {
        // If category ID is provided, verify it belongs to organization
        if ($categoryId) {
            $category = ExpenseCategory::where('id', $categoryId)
                ->where('organization_id', $organization->id)
                ->first();
            
            if ($category) {
                return $category;
            }
            
            // Category ID provided but doesn't belong to organization
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['expense_category_id' => ['The selected expense category does not belong to your organization.']]
            );
        }

        // If category name is provided, find or create it
        if ($categoryName) {
            $categoryName = trim($categoryName);
            
            if (empty($categoryName)) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['category_name' => ['Category name cannot be empty.']]
                );
            }

            // Try to find existing category by name (case-insensitive)
            $category = ExpenseCategory::where('organization_id', $organization->id)
                ->whereRaw('LOWER(name) = ?', [strtolower($categoryName)])
                ->first();

            if ($category) {
                return $category;
            }

            // Create new category
            return ExpenseCategory::create([
                'organization_id' => $organization->id,
                'name' => $categoryName,
                'description' => null,
                'is_system' => false,
            ]);
        }

        // Neither category ID nor name provided
        throw new \Illuminate\Validation\ValidationException(
            validator([], []),
            ['expense_category_id' => ['Either expense_category_id or category_name is required.']]
        );
    }
}
