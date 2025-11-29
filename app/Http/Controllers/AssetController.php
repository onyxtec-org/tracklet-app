<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/assets",
     *     summary="List assets",
     *     tags={"Assets"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"active", "in_repair", "retired"})),
     *     @OA\Parameter(name="category", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="assigned_to_user_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of assets")
     * )
     */
    public function index(Request $request)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $query = Asset::where('organization_id', $organization->id)
            ->with(['assignedToUser']);

        // Filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('assigned_to_user_id') && $request->assigned_to_user_id) {
            $query->where('assigned_to_user_id', $request->assigned_to_user_id);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('asset_code', 'like', '%' . $request->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $request->search . '%');
            });
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $categories = Asset::where('organization_id', $organization->id)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        $users = User::where('organization_id', $organization->id)->get();

        // Summary counts
        $summary = [
            'total' => Asset::where('organization_id', $organization->id)->count(),
            'active' => Asset::where('organization_id', $organization->id)->where('status', 'active')->count(),
            'in_repair' => Asset::where('organization_id', $organization->id)->where('status', 'in_repair')->count(),
            'retired' => Asset::where('organization_id', $organization->id)->where('status', 'retired')->count(),
        ];

        return $this->respond(
            [
                'assets' => $assets,
                'categories' => $categories,
                'users' => $users,
                'summary' => $summary,
                'filters' => $request->only(['status', 'category', 'assigned_to_user_id', 'search']),
            ],
            'assets.index',
            [
                'assets' => $assets,
                'categories' => $categories,
                'users' => $users,
                'summary' => $summary,
                'filters' => $request->only(['status', 'category', 'assigned_to_user_id', 'search']),
            ]
        );
    }

    /**
     * Show the form for creating a new asset.
     */
    public function create()
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $users = User::where('organization_id', $organization->id)->get();

        return $this->respond(
            ['users' => $users],
            'assets.create',
            ['users' => $users]
        );
    }

    /**
     * @OA\Post(
     *     path="/api/assets",
     *     summary="Create asset",
     *     tags={"Assets"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category", "purchase_date", "purchase_price"},
     *             @OA\Property(property="name", type="string", example="Dell Laptop"),
     *             @OA\Property(property="category", type="string", example="Electronics"),
     *             @OA\Property(property="purchase_date", type="string", format="date", example="2025-01-15"),
     *             @OA\Property(property="purchase_price", type="number", format="float", example="1200.00"),
     *             @OA\Property(property="vendor", type="string", example="Dell Inc"),
     *             @OA\Property(property="warranty_expiry", type="string", format="date"),
     *             @OA\Property(property="assigned_to_user_id", type="integer"),
     *             @OA\Property(property="assigned_to_location", type="string", example="Room 101"),
     *             @OA\Property(property="serial_number", type="string"),
     *             @OA\Property(property="model_number", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Asset created successfully")
     * )
     */
    public function store(Request $request)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'vendor' => 'nullable|string|max:255',
            'warranty_expiry' => 'nullable|date',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'assigned_to_location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Verify user belongs to organization if assigned
        if (isset($validated['assigned_to_user_id'])) {
            $user = User::where('id', $validated['assigned_to_user_id'])
                ->where('organization_id', $organization->id)
                ->firstOrFail();
        }

        DB::beginTransaction();
        try {
            // Lock all assets for this organization/year to prevent race conditions
            $prefix = strtoupper(substr($organization->slug, 0, 3));
            $year = date('Y');
            $pattern = "{$prefix}-{$year}-%";
            
            // Get all existing codes for this org/year (with lock to prevent concurrent access)
            $existingCodes = Asset::withTrashed()
                ->where('organization_id', $organization->id)
                ->where('asset_code', 'like', $pattern)
                ->lockForUpdate()
                ->pluck('asset_code')
                ->toArray();
            
            // Find the highest number
            $maxNumber = 0;
            foreach ($existingCodes as $code) {
                $number = (int) substr($code, -4);
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
            
            // Generate unique code starting from maxNumber + 1
            $maxAttempts = 20;
            $assetCode = null;
            
            for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
                $newNumber = str_pad($maxNumber + 1 + $attempt, 4, '0', STR_PAD_LEFT);
                $candidateCode = "{$prefix}-{$year}-{$newNumber}";
                
                // Double-check it doesn't exist (shouldn't with lock, but safety check)
                if (!in_array($candidateCode, $existingCodes)) {
                    // Final check in database with lock
                    $exists = Asset::withTrashed()
                        ->where('organization_id', $organization->id)
                        ->where('asset_code', $candidateCode)
                        ->lockForUpdate()
                        ->exists();
                    
                    if (!$exists) {
                        $assetCode = $candidateCode;
                        break;
                    }
                }
            }
            
            if (!$assetCode) {
                throw new \Exception("Unable to generate unique asset code after {$maxAttempts} attempts. Please try again.");
            }

            $asset = Asset::create([
                'organization_id' => $organization->id,
                'asset_code' => $assetCode,
                'name' => $validated['name'],
                'category' => $validated['category'],
                'purchase_date' => $validated['purchase_date'],
                'purchase_price' => $validated['purchase_price'],
                'vendor' => $validated['vendor'] ?? null,
                'warranty_expiry' => $validated['warranty_expiry'] ?? null,
                'status' => 'active',
                'status_changed_at' => now(),
                'assigned_to_user_id' => $validated['assigned_to_user_id'] ?? null,
                'assigned_to_location' => $validated['assigned_to_location'] ?? null,
                'description' => $validated['description'] ?? null,
                'serial_number' => $validated['serial_number'] ?? null,
                'model_number' => $validated['model_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Log initial assignment if assigned
            if ($asset->assigned_to_user_id || $asset->assigned_to_location) {
                AssetMovement::create([
                    'organization_id' => $organization->id,
                    'asset_id' => $asset->id,
                    'user_id' => auth()->id(),
                    'movement_date' => now(),
                    'movement_type' => 'assignment',
                    'to_user_id' => $asset->assigned_to_user_id,
                    'to_location' => $asset->assigned_to_location,
                    'reason' => 'Initial assignment',
                ]);
            }

            DB::commit();

            return $this->respond([
                'message' => 'Asset created successfully.',
                'asset' => $asset->load('assignedToUser'),
            ], null, [], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Check if it's a duplicate asset_code error (shouldn't happen with proper locking, but keep as safety)
            if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'asset_code')) {
                return $this->respondError('An asset with that code already exists. Please try again or contact support if the issue persists.', 400);
            }
            return $this->respondError('Failed to create asset: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondError('Failed to create asset: ' . $e->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/assets/{id}",
     *     summary="Get asset",
     *     tags={"Assets"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Asset details")
     * )
     */
    public function show(Asset $asset)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization || $asset->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        $asset->load(['assignedToUser', 'movements.user', 'maintenanceRecords']);
        $users = User::where('organization_id', $organization->id)->get();

        return $this->respond(
            ['asset' => $asset],
            'assets.show',
            ['asset' => $asset, 'users' => $users]
        );
    }

    /**
     * Show the form for editing the specified asset.
     */
    public function edit(Asset $asset)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization || $asset->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        $users = User::where('organization_id', $organization->id)->get();

        return $this->respond(
            [
                'asset' => $asset,
                'users' => $users,
            ],
            'assets.edit',
            [
                'asset' => $asset,
                'users' => $users,
            ]
        );
    }

    /**
     * @OA\Put(
     *     path="/api/assets/{id}",
     *     summary="Update asset",
     *     tags={"Assets"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category", "purchase_date", "purchase_price", "status"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="category", type="string"),
     *             @OA\Property(property="purchase_date", type="string", format="date"),
     *             @OA\Property(property="purchase_price", type="number", format="float"),
     *             @OA\Property(property="status", type="string", enum={"active", "in_repair", "retired"}),
     *             @OA\Property(property="status_change_reason", type="string"),
     *             @OA\Property(property="assigned_to_user_id", type="integer"),
     *             @OA\Property(property="assigned_to_location", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Asset updated successfully")
     * )
     */
    public function update(Request $request, Asset $asset)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization || $asset->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'vendor' => 'nullable|string|max:255',
            'warranty_expiry' => 'nullable|date',
            'status' => 'required|in:active,in_repair,retired',
            'status_change_reason' => 'nullable|string|required_if:status,retired',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'assigned_to_location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Verify user belongs to organization if assigned
        if (isset($validated['assigned_to_user_id'])) {
            $user = User::where('id', $validated['assigned_to_user_id'])
                ->where('organization_id', $organization->id)
                ->firstOrFail();
        }

        DB::beginTransaction();
        try {
            // Track status change
            $statusChanged = $asset->status !== $validated['status'];
            $assignmentChanged = $asset->assigned_to_user_id != ($validated['assigned_to_user_id'] ?? null) ||
                                 $asset->assigned_to_location != ($validated['assigned_to_location'] ?? null);

            if ($statusChanged) {
                $validated['status_changed_at'] = now();
            }

            $asset->update($validated);

            // Log movement if assignment changed
            if ($assignmentChanged) {
                AssetMovement::create([
                    'organization_id' => $organization->id,
                    'asset_id' => $asset->id,
                    'user_id' => auth()->id(),
                    'movement_date' => now(),
                    'movement_type' => $asset->assigned_to_user_id ? 'assignment' : 'location_change',
                    'from_user_id' => $asset->getOriginal('assigned_to_user_id'),
                    'from_location' => $asset->getOriginal('assigned_to_location'),
                    'to_user_id' => $asset->assigned_to_user_id,
                    'to_location' => $asset->assigned_to_location,
                    'reason' => 'Asset updated',
                ]);
            }

            DB::commit();

            return $this->respond([
                'message' => 'Asset updated successfully.',
                'asset' => $asset->fresh()->load('assignedToUser'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondError('Failed to update asset: ' . $e->getMessage(), 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/assets/{id}",
     *     summary="Delete asset",
     *     tags={"Assets"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Asset deleted successfully")
     * )
     */
    public function destroy(Asset $asset)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization || $asset->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        $asset->delete();

        return $this->respond([
            'message' => 'Asset deleted successfully.',
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/assets/{id}/movement",
     *     summary="Log asset movement",
     *     tags={"Assets"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"movement_date", "movement_type"},
     *             @OA\Property(property="movement_date", type="string", format="date", example="2025-11-28"),
     *             @OA\Property(property="movement_type", type="string", enum={"assignment", "location_change", "return", "other"}, example="assignment"),
     *             @OA\Property(property="from_user_id", type="integer"),
     *             @OA\Property(property="from_location", type="string"),
     *             @OA\Property(property="to_user_id", type="integer"),
     *             @OA\Property(property="to_location", type="string", example="Room 102"),
     *             @OA\Property(property="reason", type="string", example="Department transfer"),
     *             @OA\Property(property="notes", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Movement logged successfully")
     * )
     */
    public function logMovement(Request $request, Asset $asset)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization || $asset->organization_id !== $organization->id) {
            return $this->respondError('Unauthorized access.', 403);
        }

        $validated = $request->validate([
            'movement_date' => 'required|date',
            'movement_type' => 'required|in:assignment,location_change,return,other',
            'from_user_id' => 'nullable|exists:users,id',
            'from_location' => 'nullable|string|max:255',
            'to_user_id' => 'nullable|exists:users,id',
            'to_location' => 'nullable|string|max:255',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Verify users belong to organization
        if (isset($validated['from_user_id'])) {
            User::where('id', $validated['from_user_id'])
                ->where('organization_id', $organization->id)
                ->firstOrFail();
        }

        if (isset($validated['to_user_id'])) {
            User::where('id', $validated['to_user_id'])
                ->where('organization_id', $organization->id)
                ->firstOrFail();
        }

        $movement = AssetMovement::create([
            'organization_id' => $organization->id,
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'movement_date' => $validated['movement_date'],
            'movement_type' => $validated['movement_type'],
            'from_user_id' => $validated['from_user_id'] ?? null,
            'from_location' => $validated['from_location'] ?? null,
            'to_user_id' => $validated['to_user_id'] ?? null,
            'to_location' => $validated['to_location'] ?? null,
            'reason' => $validated['reason'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update asset assignment if movement type is assignment
        if ($validated['movement_type'] === 'assignment') {
            $asset->update([
                'assigned_to_user_id' => $validated['to_user_id'] ?? null,
                'assigned_to_location' => $validated['to_location'] ?? null,
            ]);
        }

        return $this->respond([
            'message' => 'Asset movement logged successfully.',
            'movement' => $movement->load(['fromUser', 'toUser']),
        ], null, [], 201);
    }
}
