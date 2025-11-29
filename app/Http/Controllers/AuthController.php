<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register new organization",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"organization_name", "name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="organization_name", type="string", example="Acme Corp"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="admin@acme.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Organization registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="1|abc123..."),
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="organization", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_name' => 'required|string|max:255',
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s]+$/|min:2',
            'email' => 'required|email:rfc,dns|unique:users,email|unique:organizations,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.regex' => 'The name field may only contain letters, numbers, and spaces. Special characters are not allowed.',
            'name.min' => 'The name must be at least 2 characters.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        if ($validator->fails()) {
            return $this->respondError('Validation failed.', 422, $validator->errors()->toArray());
        }

        try {
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
                'must_change_password' => false,
            ]);

            // Assign admin role
            $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
            if ($adminRole) {
                $user->assignRole($adminRole);
            }

            // For API requests, return token
            if ($request->expectsJson() || $request->is('api/*')) {
                $token = $user->createToken('auth-token')->plainTextToken;
                
                return response()->json([
                    'success' => true,
                    'message' => 'Organization registered successfully! Please complete your subscription.',
                    'data' => [
                        'user' => $user->load('roles', 'organization'),
                        'token' => $token,
                        'token_type' => 'Bearer',
                        'must_change_password' => false,
                        'redirect' => route('subscription.checkout'),
                    ],
                ], 201);
            }

            // For web requests, auto-login and redirect
            Auth::login($user);

            return $this->respond([
                'message' => 'Organization registered successfully! Please complete your subscription.',
                'redirect' => route('subscription.checkout'),
            ]);

        } catch (\Exception $e) {
            return $this->respondError('Failed to register organization: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@acme.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="1|abc123..."),
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="must_change_password", type="boolean", example=false)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->respondError('Validation failed.', 422, $validator->errors()->toArray());
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember', true); // Default to true for persistent login

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            $user->load('roles', 'organization');

            // Check if user must change password
            $mustChangePassword = $user->must_change_password ?? false;

            // For API requests, return token
            if ($request->expectsJson() || $request->is('api/*')) {
                $token = $user->createToken('auth-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'user' => $user,
                        'token' => $token,
                        'token_type' => 'Bearer',
                        'must_change_password' => $mustChangePassword,
                    ],
                ], 200);
            }

            // For web requests, check password change requirement
            if ($mustChangePassword) {
                return redirect()->route('password.change')->with('warning', 'You must change your password before continuing.');
            }

            return redirect()->intended('/');
        }

        return $this->respondError('Invalid credentials.', 401);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Logged out successfully")
     * )
     */
    public function logout(Request $request)
    {
        // For API requests, revoke token
        if ($request->expectsJson() || $request->is('api/*')) {
            $request->user()->currentAccessToken()->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ], 200);
        }

        // For web requests, logout session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get current user",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User data",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="admin@acme.com"),
     *                     @OA\Property(property="organization", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $user->load('roles', 'organization');

        return $this->respond([
            'user' => $user,
            'must_change_password' => $user->must_change_password ?? false,
        ]);
    }

    /**
     * Show password change form
     */
    public function showChangePasswordForm()
    {
        $user = auth()->user();
        
        if (!$user->must_change_password) {
            return redirect()->route('dashboard.index');
        }

        $pageConfigs = [
            'bodyClass' => "bg-full-screen-image",
            'blankPage' => true
        ];

        return view('auth.change-password', [
            'pageConfigs' => $pageConfigs,
            'user' => $user,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/change-password",
     *     summary="Change password",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "password", "password_confirmation"},
     *             @OA\Property(property="current_password", type="string", format="password"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Password changed successfully")
     * )
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->respondError('Validation failed.', 422, $validator->errors()->toArray());
            }
            return back()->withErrors($validator)->withInput();
        }

        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->respondError('Current password is incorrect.', 422);
            }
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
            'must_change_password' => false,
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->respond([
                'message' => 'Password changed successfully.',
                'user' => $user->fresh()->load('roles', 'organization'),
            ]);
        }

        return redirect()->route('dashboard.index')->with('success', 'Password changed successfully. You can now access all features.');
    }
}
