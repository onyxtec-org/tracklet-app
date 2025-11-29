<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/inventory/items",
     *     summary="List inventory items",
     *     tags={"Inventory"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="category", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="low_stock", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of inventory items"),
     *     @OA\Response(response=403, description="Super Admin cannot access inventory management")
     * )
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $organization = $user->organization;
        
        // Super Admin should not access inventory (organization-specific feature)
        if ($user->isSuperAdmin()) {
            return $this->respondError('Super Admin cannot access inventory management. Please use an organization account.', 403);
        }
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $query = InventoryItem::where('organization_id', $organization->id);

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter low stock items
        if ($request->has('low_stock') && $request->low_stock) {
            $query->whereRaw('quantity <= minimum_threshold');
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $query->orderBy('name')->paginate(20);

        // Get all categories for filter
        $categories = InventoryItem::where('organization_id', $organization->id)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        // Get low stock items count
        $lowStockCount = InventoryItem::where('organization_id', $organization->id)
            ->whereRaw('quantity <= minimum_threshold')
            ->count();

        return $this->respond(
            [
                'items' => $items,
                'categories' => $categories,
                'low_stock_count' => $lowStockCount,
                'filters' => $request->only(['category', 'low_stock', 'search']),
            ],
            'inventory.index',
            [
                'items' => $items,
                'categories' => $categories,
                'low_stock_count' => $lowStockCount,
                'filters' => $request->only(['category', 'low_stock', 'search']),
            ]
        );
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        return $this->respond(
            null,
            'inventory.create'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/inventory/items",
     *     summary="Create inventory item",
     *     tags={"Inventory"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "quantity", "minimum_threshold", "unit_price"},
     *             @OA\Property(property="name", type="string", example="A4 Paper"),
     *             @OA\Property(property="category", type="string", example="Office Supplies"),
     *             @OA\Property(property="quantity", type="integer", example=100, description="Initial stock quantity"),
     *             @OA\Property(property="minimum_threshold", type="integer", example=20),
     *             @OA\Property(property="unit_price", type="number", format="float", example="5.00"),
     *             @OA\Property(property="unit", type="string", example="reams")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Item created successfully"),
     *     @OA\Response(response=403, description="Super Admin cannot access inventory management")
     * )
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $organization = $user->organization;
        
        // Super Admin should not access inventory
        if ($user->isSuperAdmin()) {
            return $this->respondError('Super Admin cannot access inventory management. Please use an organization account.', 403);
        }
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'minimum_threshold' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:1',
            'unit' => 'nullable|string|max:50',
        ]);

        $item = new InventoryItem($validated);
        $item->organization_id = $organization->id;
        $item->calculateTotalPrice();
        $item->save();

        return $this->respond([
            'message' => 'Inventory item created successfully.',
            'item' => $item,
            'redirect' => route('inventory.items.index'),
        ], null, [], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/inventory/items/{id}",
     *     summary="Get inventory item",
     *     tags={"Inventory"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Item details"),
     *     @OA\Response(response=403, description="Super Admin cannot access inventory management")
     * )
     */
    public function show(InventoryItem $inventoryItem)
    {
        $user = auth()->user();
        
        // Super Admin should not access inventory
        if ($user->isSuperAdmin()) {
            return $this->respondError('Super Admin cannot access inventory management. Please use an organization account.', 403);
        }
        
        // Ensure organization is loaded
        $user->load('organization');
        $organization = $user->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }
        
        // Reload the item to ensure we have the latest data
        $inventoryItem->refresh();
        
        if ($inventoryItem->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access. This item belongs to a different organization.', 403);
        }

        $inventoryItem->load(['stockTransactions.user']);

        return $this->respond(
            ['item' => $inventoryItem],
            'inventory.show',
            ['item' => $inventoryItem]
        );
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit(InventoryItem $inventoryItem)
    {
        $user = auth()->user();
        
        // Super Admin should not access inventory
        if ($user->isSuperAdmin()) {
            return $this->respondError('Super Admin cannot access inventory management. Please use an organization account.', 403);
        }
        
        // Ensure organization is loaded
        $user->load('organization');
        $organization = $user->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }
        
        // Reload the item to ensure we have the latest data
        $inventoryItem->refresh();
        
        if ($inventoryItem->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access. This item belongs to a different organization.', 403);
        }

        return $this->respond(
            ['item' => $inventoryItem],
            'inventory.edit',
            ['item' => $inventoryItem]
        );
    }

    /**
     * @OA\Put(
     *     path="/api/inventory/items/{id}",
     *     summary="Update inventory item",
     *     tags={"Inventory"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "quantity", "minimum_threshold", "unit_price"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="category", type="string"),
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="minimum_threshold", type="integer"),
     *             @OA\Property(property="unit_price", type="number", format="float"),
     *             @OA\Property(property="unit", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Item updated successfully"),
     *     @OA\Response(response=403, description="Super Admin cannot access inventory management")
     * )
     */
    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $user = auth()->user();
        $organization = $user->organization;
        
        // Super Admin should not access inventory
        if ($user->isSuperAdmin()) {
            return $this->respondError('Super Admin cannot access inventory management. Please use an organization account.', 403);
        }
        
        if (!$organization || $inventoryItem->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'minimum_threshold' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:1',
            'unit' => 'nullable|string|max:50',
        ]);

        $inventoryItem->fill($validated);
        $inventoryItem->calculateTotalPrice();
        $inventoryItem->save();

        return $this->respond([
            'message' => 'Inventory item updated successfully.',
            'item' => $inventoryItem->fresh(),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/inventory/items/{id}",
     *     summary="Delete inventory item",
     *     tags={"Inventory"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Item deleted successfully"),
     *     @OA\Response(response=403, description="Super Admin cannot access inventory management")
     * )
     */
    public function destroy(InventoryItem $inventoryItem)
    {
        $user = auth()->user();
        $organization = $user->organization;
        
        // Super Admin should not access inventory
        if ($user->isSuperAdmin()) {
            return $this->respondError('Super Admin cannot access inventory management. Please use an organization account.', 403);
        }
        
        if (!$organization || $inventoryItem->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        $inventoryItem->delete();

        return $this->respond([
            'message' => 'Inventory item deleted successfully.',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/inventory/items/{id}/stock",
     *     summary="Log stock transaction (Stock In/Out)",
     *     tags={"Inventory"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type", "quantity", "transaction_date"},
     *             @OA\Property(property="type", type="string", enum={"in", "out"}, example="in", description="'in' = Stock In (add inventory), 'out' = Stock Out (remove inventory). Automatically updates item quantity."),
     *             @OA\Property(property="quantity", type="integer", example=50, minimum=1),
     *             @OA\Property(property="transaction_date", type="string", format="date", example="2025-11-28"),
     *             @OA\Property(property="reference", type="string", example="PO-12345", description="Purchase order, usage reason, etc."),
     *             @OA\Property(property="notes", type="string"),
     *             @OA\Property(property="unit_price", type="number", format="float", description="For stock in only - updates item unit price if provided"),
     *             @OA\Property(property="vendor", type="string", description="For stock in only - supplier name")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Transaction logged successfully. Item quantity updated automatically."),
     *     @OA\Response(response=400, description="Insufficient stock (for stock out)"),
     *     @OA\Response(response=403, description="Super Admin cannot access inventory management")
     * )
     */
    public function logStock(Request $request, InventoryItem $inventoryItem)
    {
        $user = auth()->user();
        $organization = $user->organization;
        
        // Super Admin should not access inventory
        if ($user->isSuperAdmin()) {
            return $this->respondError('Super Admin cannot access inventory management. Please use an organization account.', 403);
        }
        
        if (!$organization || $inventoryItem->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        $validated = $request->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'transaction_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'unit_price' => 'nullable|numeric|min:0', // For stock in
            'vendor' => 'nullable|string|max:255', // For stock in
        ]);

        DB::beginTransaction();
        try {
            // Create stock transaction
            $transaction = StockTransaction::create([
                'organization_id' => $organization->id,
                'inventory_item_id' => $inventoryItem->id,
                'user_id' => auth()->id(),
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'transaction_date' => $validated['transaction_date'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'unit_price' => $validated['unit_price'] ?? null,
                'vendor' => $validated['vendor'] ?? null,
            ]);

            // Update inventory item quantity
            if ($validated['type'] === 'in') {
                $inventoryItem->quantity += $validated['quantity'];
                // Update unit price if provided
                if (isset($validated['unit_price'])) {
                    $inventoryItem->unit_price = $validated['unit_price'];
                }
            } else {
                $inventoryItem->quantity -= $validated['quantity'];
                if ($inventoryItem->quantity < 0) {
                    throw new \Exception('Insufficient stock. Available: ' . ($inventoryItem->quantity + $validated['quantity']));
                }
            }

            $inventoryItem->calculateTotalPrice();
            $inventoryItem->save();

            DB::commit();

            return $this->respond([
                'message' => 'Stock transaction logged successfully.',
                'transaction' => $transaction->load('user'),
                'item' => $inventoryItem->fresh(),
            ], null, [], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondError('Failed to log stock transaction: ' . $e->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/inventory/items/{id}/transactions",
     *     summary="Get stock transactions",
     *     tags={"Inventory"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="type", in="query", @OA\Schema(type="string", enum={"in", "out"})),
     *     @OA\Parameter(name="date_from", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="date_to", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Stock transactions")
     * )
     */
    public function stockTransactions(InventoryItem $inventoryItem, Request $request)
    {
        $user = auth()->user();
        $organization = $user->organization;
        
        // Super Admin should not access inventory
        if ($user->isSuperAdmin()) {
            return $this->respondError('Super Admin cannot access inventory management. Please use an organization account.', 403);
        }
        
        if (!$organization || $inventoryItem->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        $query = $inventoryItem->stockTransactions()->with('user');

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        return $this->respond(
            [
                'item' => $inventoryItem,
                'transactions' => $transactions,
            ],
            'inventory.transactions',
            [
                'item' => $inventoryItem,
                'transactions' => $transactions,
            ]
        );
    }

    /**
     * @OA\Get(
     *     path="/api/inventory/low-stock",
     *     summary="Get low stock items",
     *     tags={"Inventory"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Low stock items")
     * )
     */
    public function lowStock()
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $items = InventoryItem::where('organization_id', $organization->id)
            ->whereRaw('quantity <= minimum_threshold')
            ->orderBy('quantity', 'asc')
            ->get();

        return $this->respond(
            ['items' => $items],
            'inventory.low-stock',
            ['items' => $items]
        );
    }

    /**
     * @OA\Get(
     *     path="/api/inventory/purchase-history",
     *     summary="Get purchase history",
     *     tags={"Inventory"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="item_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="date_from", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="date_to", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Purchase history")
     * )
     */
    public function purchaseHistory(Request $request)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $query = StockTransaction::where('organization_id', $organization->id)
            ->where('type', 'in')
            ->with(['inventoryItem', 'user']);

        if ($request->has('item_id') && $request->item_id) {
            $query->where('inventory_item_id', $request->item_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        return $this->respond(
            ['transactions' => $transactions],
            'inventory.purchase-history',
            ['transactions' => $transactions]
        );
    }

    /**
     * @OA\Get(
     *     path="/api/inventory/aging-report",
     *     summary="Get item aging report",
     *     tags={"Inventory"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Aging report")
     * )
     */
    public function agingReport()
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $items = InventoryItem::where('organization_id', $organization->id)
            ->where('quantity', '>', 0)
            ->get()
            ->map(function ($item) {
                $oldestDate = $item->getOldestStockDate();
                $ageInDays = Carbon::parse($oldestDate)->diffInDays(now());
                
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'category' => $item->category,
                    'quantity' => $item->quantity,
                    'oldest_stock_date' => $oldestDate,
                    'age_in_days' => $ageInDays,
                ];
            })
            ->sortByDesc('age_in_days')
            ->values();

        return $this->respond(
            ['items' => $items],
            'inventory.aging-report',
            ['items' => $items]
        );
    }
}
