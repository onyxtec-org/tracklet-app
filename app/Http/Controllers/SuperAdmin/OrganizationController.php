<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\OrganizationInvitationMail;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/super-admin/organizations",
     *     summary="List organizations (Super Admin)",
     *     tags={"Super Admin"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="List of organizations")
     * )
     */
    public function index()
    {
        if (request()->ajax() || request()->expectsJson()) {
            $organizations = Organization::with(['users.roles', 'invitations'])
                ->latest()
                ->get()
                ->map(function ($org) {
                    // Get latest invitation
                    $latestInvitation = $org->invitations->sortByDesc('created_at')->first();
                    
                    // Determine invitation status
                    $invitationStatus = 'none';
                    if ($latestInvitation) {
                        if ($latestInvitation->isAccepted()) {
                            $invitationStatus = 'joined';
                        } elseif ($latestInvitation->isExpired()) {
                            $invitationStatus = 'expired';
                        } else {
                            $invitationStatus = 'pending';
                        }
                    }
                    
                    // Determine overall status
                    $overallStatus = 'pending';
                    $overallStatusLabel = 'Pending';
                    
                    if ($org->registration_source === 'self_registered') {
                        if ($org->is_subscribed) {
                            $overallStatus = 'subscribed';
                            $overallStatusLabel = 'Subscribed';
                        } elseif ($org->users->count() > 0) {
                            $overallStatus = 'joined';
                            $overallStatusLabel = 'Joined (Not Subscribed)';
                        } else {
                            $overallStatus = 'registered';
                            $overallStatusLabel = 'Registered';
                        }
                    } else {
                        // Invited organizations
                        if ($org->is_subscribed) {
                            $overallStatus = 'subscribed';
                            $overallStatusLabel = 'Subscribed';
                        } elseif ($invitationStatus === 'joined') {
                            $overallStatus = 'joined';
                            $overallStatusLabel = 'Joined (Not Subscribed)';
                        } elseif ($invitationStatus === 'pending') {
                            $overallStatus = 'pending';
                            $overallStatusLabel = 'Pending Invitation';
                        } elseif ($invitationStatus === 'expired') {
                            $overallStatus = 'expired';
                            $overallStatusLabel = 'Invitation Expired';
                        } else {
                            $overallStatus = 'pending';
                            $overallStatusLabel = 'Pending';
                        }
                    }
                    
                    // Get admin user
                    $admin = $org->users->first(function ($user) {
                        return $user->hasRole('admin');
                    });
                    
                    // Check if on trial
                    $isOnTrial = $org->trial_ends_at && $org->trial_ends_at->isFuture();
                    
                    return [
                        'id' => $org->id,
                        'name' => $org->name,
                        'email' => $org->email,
                        'admin' => $admin ? ['name' => $admin->name, 'email' => $admin->email] : null,
                        'is_active' => $org->is_active,
                        'is_subscribed' => $org->is_subscribed,
                        'is_on_trial' => $isOnTrial,
                        'registration_source' => $org->registration_source ?? 'invited',
                        'invitation_status' => $invitationStatus,
                        'overall_status' => $overallStatus,
                        'overall_status_label' => $overallStatusLabel,
                        'invitation_sent_at' => $latestInvitation ? $latestInvitation->created_at : null,
                        'invitation_accepted_at' => $latestInvitation ? $latestInvitation->accepted_at : null,
                        'created_at' => $org->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $organizations,
            ]);
        }

        $organizations = Organization::with(['users.roles', 'invitations'])
            ->latest()
            ->paginate(15);

        return $this->respond(
            ['organizations' => $organizations],
            'superadmin.organizations.index',
            ['organizations' => $organizations]
        );
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create()
    {
        return $this->respond(
            null,
            'superadmin.organizations.create'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/super-admin/organizations",
     *     summary="Create organization (Super Admin)",
     *     tags={"Super Admin"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="Acme Corp"),
     *             @OA\Property(property="email", type="string", format="email", example="admin@acme.com")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Organization created and invitation sent")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:organization_invitations,email',
        ]);

        try {
            DB::beginTransaction();

            // Generate slug from organization name
            $slug = Str::slug($validated['name']);
            
            // Ensure slug is unique
            $baseSlug = $slug;
            $counter = 1;
            while (Organization::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Create organization (invited)
            $organization = Organization::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'email' => $validated['email'],
                'is_subscribed' => false,
                'is_active' => true,
                'registration_source' => 'invited',
            ]);

            // Create invitation
            $invitation = OrganizationInvitation::create([
                'organization_id' => $organization->id,
                'email' => $validated['email'],
                'invited_by' => auth()->id(),
                'expires_at' => now()->addDays(7),
            ]);

            // Send invitation email
            Mail::to($validated['email'])->send(new OrganizationInvitationMail($invitation));

            DB::commit();

            return $this->respond([
                'message' => 'Organization created and invitation sent successfully.',
                'organization' => $organization,
                'invitation' => $invitation,
            ], null, [], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondError('Failed to create organization: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/super-admin/organizations/{id}",
     *     summary="Get organization (Super Admin)",
     *     tags={"Super Admin"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Organization details")
     * )
     */
    public function show(Organization $organization)
    {
        $organization->load(['users.roles', 'invitations']);

        return $this->respond(
            ['organization' => $organization],
            'superadmin.organizations.show',
            ['organization' => $organization]
        );
    }

    /**
     * Show the form for editing the specified organization.
     */
    public function edit(Organization $organization)
    {
        return $this->respond(
            ['organization' => $organization],
            'superadmin.organizations.edit',
            ['organization' => $organization]
        );
    }

    /**
     * @OA\Put(
     *     path="/api/super-admin/organizations/{id}",
     *     summary="Update organization (Super Admin)",
     *     tags={"Super Admin"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Organization updated successfully")
     * )
     */
    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('organizations', 'slug')->ignore($organization->id),
            ],
            'is_active' => 'boolean',
        ]);

        $organization->update($validated);

        return $this->respond([
            'message' => 'Organization updated successfully.',
            'organization' => $organization->fresh(),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/super-admin/organizations/{id}",
     *     summary="Delete organization (Super Admin)",
     *     tags={"Super Admin"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Organization deleted successfully")
     * )
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();

        return $this->respond([
            'message' => 'Organization deleted successfully.',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/super-admin/organizations/{id}/resend-invitation",
     *     summary="Resend invitation (Super Admin)",
     *     tags={"Super Admin"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Invitation resent successfully")
     * )
     */
    public function resendInvitation(Organization $organization)
    {
        $latestInvitation = $organization->invitations()
            ->where('email', $organization->email)
            ->latest()
            ->first();

        if (!$latestInvitation) {
            return $this->respondError('No invitation found for this organization.', 404);
        }

        if ($latestInvitation->isAccepted()) {
            return $this->respondError('Invitation has already been accepted.', 400);
        }

        // Create new invitation
        $invitation = OrganizationInvitation::create([
            'organization_id' => $organization->id,
            'email' => $organization->email,
            'invited_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($organization->email)->send(new OrganizationInvitationMail($invitation));

        return $this->respond([
            'message' => 'Invitation resent successfully.',
            'invitation' => $invitation,
        ]);
    }
}
