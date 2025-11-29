<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\UserInvitationMail;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="List users",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="role", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of users")
     * )
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $organization = $user->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        // Super Admin can see all users, Admin can only see their organization's users
        $query = User::with('roles');
        
        if (!$user->isSuperAdmin()) {
            $query->where('organization_id', $organization->id);
        } else {
            // Super Admin can filter by organization
            if ($request->has('organization_id') && $request->organization_id) {
                $query->where('organization_id', $request->organization_id);
            }
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('name')->paginate(20);

        // Get available roles (exclude super_admin for organization admins)
        $availableRoles = Role::where('name', '!=', 'super_admin')->get();

        return $this->respond(
            [
                'users' => $users,
                'roles' => $availableRoles,
                'organization' => $organization,
                'filters' => $request->only(['role', 'search']),
            ],
            'users.index',
            [
                'users' => $users,
                'roles' => $availableRoles,
                'organization' => $organization,
                'filters' => $request->only(['role', 'search']),
            ]
        );
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $user = auth()->user();
        $organization = $user->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        // Get available roles (exclude super_admin)
        $roles = Role::where('name', '!=', 'super_admin')->get();

        return $this->respond(
            ['roles' => $roles],
            'users.create',
            ['roles' => $roles]
        );
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create user",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "role"},
     *             @OA\Property(property="name", type="string", example="Jane Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="jane@acme.com"),
     *             @OA\Property(property="role", type="string", example="finance", enum={"admin", "finance", "admin_support", "general_staff"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created successfully"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $organization = $user->organization;
        
        if (!$organization) {
            return $this->respondError('User does not belong to an organization.', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|exists:roles,name',
        ]);

        // Prevent assigning super_admin role
        if ($validated['role'] === 'super_admin') {
            return $this->respondError('Cannot assign super_admin role.', 403);
        }

        // Verify role exists and is not super_admin
        $role = Role::where('name', $validated['role'])
            ->where('name', '!=', 'super_admin')
            ->firstOrFail();

        // Generate random password
        $randomPassword = Str::random(12);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($randomPassword),
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
            'must_change_password' => true, // User must change password on first login
        ]);

        // Assign role
        $newUser->assignRole($role);

        // Send invitation email with password
        try {
            Mail::to($newUser->email)->send(new UserInvitationMail($newUser, $randomPassword));
        } catch (\Exception $e) {
            // Log error but don't fail user creation
            \Log::error('Failed to send user invitation email: ' . $e->getMessage());
        }

        return $this->respond([
            'message' => 'User created successfully. An email with login credentials has been sent.',
            'user' => $newUser->load('roles'),
        ], null, [], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $currentUser = auth()->user();
        $organization = $currentUser->organization;
        
        // Super Admin can view any user, others can only view users in their organization
        if (!$currentUser->isSuperAdmin()) {
            if (!$organization || $user->organization_id !== $organization->id) {
                return $this->respondError('Unauthorized access.', 403);
            }
        }

        $user->load('roles', 'organization');

        return $this->respond(
            ['user' => $user],
            'users.show',
            ['user' => $user]
        );
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $currentUser = auth()->user();
        $organization = $currentUser->organization;
        
        // Super Admin can edit any user, others can only edit users in their organization
        if (!$currentUser->isSuperAdmin()) {
            if (!$organization || $user->organization_id !== $organization->id) {
                return $this->respondError('Unauthorized access.', 403);
            }
        }

        // Prevent editing super_admin users (unless current user is super_admin)
        if ($user->isSuperAdmin() && !$currentUser->isSuperAdmin()) {
            return $this->respondError('Cannot edit super admin user.', 403);
        }

        // Get available roles (exclude super_admin for non-super-admin users)
        $roles = Role::where('name', '!=', 'super_admin')->get();

        $user->load('roles');

        return $this->respond(
            [
                'user' => $user,
                'roles' => $roles,
            ],
            'users.edit',
            [
                'user' => $user,
                'roles' => $roles,
            ]
        );
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $organization = $currentUser->organization;
        
        // Super Admin can update any user, others can only update users in their organization
        if (!$currentUser->isSuperAdmin()) {
            if (!$organization || $user->organization_id !== $organization->id) {
                return $this->respondError('Unauthorized access.', 403);
            }
        }

        // Prevent editing super_admin users (unless current user is super_admin)
        if ($user->isSuperAdmin() && !$currentUser->isSuperAdmin()) {
            return $this->respondError('Cannot edit super admin user.', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
        ]);

        // Prevent assigning super_admin role
        if ($validated['role'] === 'super_admin' && !$currentUser->isSuperAdmin()) {
            return $this->respondError('Cannot assign super_admin role.', 403);
        }

        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update role
        $role = Role::where('name', $validated['role'])->firstOrFail();
        $user->syncRoles([$role]);

        return $this->respond([
            'message' => 'User updated successfully.',
            'user' => $user->fresh()->load('roles'),
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();
        $organization = $currentUser->organization;
        
        // Super Admin can delete any user, others can only delete users in their organization
        if (!$currentUser->isSuperAdmin()) {
            if (!$organization || $user->organization_id !== $organization->id) {
                return $this->respondError('Unauthorized access.', 403);
            }
        }

        // Prevent deleting super_admin users
        if ($user->isSuperAdmin()) {
            return $this->respondError('Cannot delete super admin user.', 403);
        }

        // Prevent deleting yourself
        if ($user->id === $currentUser->id) {
            return $this->respondError('Cannot delete your own account.', 403);
        }

        $user->delete();

        return $this->respond([
            'message' => 'User deleted successfully.',
        ]);
    }
}
