<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="Get available roles",
     *     tags={"Roles"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of available roles",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="roles", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="admin"),
     *                         @OA\Property(property="display_name", type="string", example="Admin"),
     *                         @OA\Property(property="description", type="string", example="Full access within organization")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Super admin can see all roles, others exclude super_admin
        if ($user->isSuperAdmin()) {
            $roles = Role::orderBy('name')->get();
        } else {
            $roles = Role::where('name', '!=', 'super_admin')
                ->orderBy('name')
                ->get();
        }

        // Format roles with descriptions
        $formattedRoles = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => ucfirst(str_replace('_', ' ', $role->name)),
                'description' => $this->getRoleDescription($role->name),
            ];
        });

        return $this->respond([
            'roles' => $formattedRoles,
        ]);
    }

    /**
     * Get role description
     */
    private function getRoleDescription($roleName)
    {
        $descriptions = [
            'super_admin' => 'Full access across all organizations',
            'admin' => 'Full access within organization',
            'finance' => 'Access to Expense Tracking Module',
            'admin_support' => 'Access to Inventory, Assets, and Maintenance modules',
            'general_staff' => 'Read-only access to relevant views',
        ];

        return $descriptions[$roleName] ?? 'No description available';
    }
}

