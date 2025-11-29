<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Traits\ApiResponse;

class ProfileController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Get current user profile",
     *     tags={"Profile"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="organization", type="object", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
        $user = auth()->user();
        $user->load('organization', 'roles');
        
        return $this->respond(
            ['user' => $user],
            'profile',
            ['user' => $user]
        );
    }

    /**
     * @OA\Put(
     *     path="/api/profile",
     *     summary="Update current user profile",
     *     tags={"Profile"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="John Doe", description="User's full name (letters, numbers, and spaces only, min 2 characters)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s]+$/|min:2',
        ], [
            'name.regex' => 'The name field may only contain letters, numbers, and spaces. Special characters are not allowed.',
            'name.min' => 'The name must be at least 2 characters.',
        ]);

        $user->name = $validated['name'];

        if ($user->save()) {
            return $this->respond([
                'message' => 'Profile updated successfully',
                'user' => $user->fresh()->load('organization', 'roles')
            ]);
        } else {
            return $this->respondError('Error while updating profile', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/profile/password",
     *     summary="Update current user password",
     *     tags={"Profile"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"old-password", "new-password", "new-password_confirmation"},
     *             @OA\Property(property="old-password", type="string", format="password", example="oldpassword123", description="Current password"),
     *             @OA\Property(property="new-password", type="string", format="password", example="newpassword123", description="New password (min 8 characters, must be different from old password)"),
     *             @OA\Property(property="new-password_confirmation", type="string", format="password", example="newpassword123", description="Confirm new password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function updatePasswordProfile(Request $request)
    {
        $user = auth()->user();
        
        $validator = \Validator::make($request->all(), [
            'old-password' => [
                'required', 'min:8', 'max:255',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Old Password is incorrect!');
                    }
                },
            ],
            'new-password' => 'required|min:8|max:255|confirmed|different:old-password',
            'new-password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->respondError('Validation failed.', 422, $validator->errors()->toArray());
        }

        $user->password = Hash::make($request->input('new-password'));

        if ($user->save()) {
            return $this->respond([
                'message' => 'Password updated successfully'
            ]);
        } else {
            return $this->respondError('Error while updating password', 500);
        }
    }
}
