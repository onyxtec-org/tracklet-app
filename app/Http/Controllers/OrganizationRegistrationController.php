<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class OrganizationRegistrationController extends Controller
{
    use ApiResponse;

    /**
     * Show organization registration form
     */
    public function show()
    {
        return $this->respond(
            null,
            'auth.register-organization'
        );
    }

    /**
     * Register new organization and create admin account
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:organizations,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->respondError('Validation failed.', 422, $validator->errors()->toArray());
        }

        try {
            DB::beginTransaction();

            // Generate slug from organization name
            $slug = Str::slug($request->organization_name);

            // Ensure slug is unique
            $baseSlug = $slug;
            $counter = 1;
            while (Organization::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Create organization (self-registered)
            $organization = Organization::create([
                'name' => $request->organization_name,
                'slug' => $slug,
                'email' => $request->email,
                'is_subscribed' => false,
                'is_active' => true,
                'registration_source' => 'self_registered',
            ]);

            // Create admin user account (first account = admin)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'organization_id' => $organization->id,
                'email_verified_at' => now(),
            ]);

            // Assign admin role - Organization's first account becomes admin
            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole) {
                $user->assignRole($adminRole);
            }

            DB::commit();

            // Auto-login the user
            auth()->login($user);

            return $this->respond([
                'message' => 'Organization registered successfully! Please complete your subscription.',
                'redirect' => route('subscription.checkout'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondError('Failed to register organization: ' . $e->getMessage(), 500);
        }
    }
}
