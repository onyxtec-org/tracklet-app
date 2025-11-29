<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords, ApiResponse;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * @OA\Post(
     *     path="/api/reset-password",
     *     summary="Reset password with verification token (API) or reset token (Web)",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password", "password_confirmation"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="User's email address"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123", description="New password (min 8 characters)"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123", description="Confirm new password"),
     *             @OA\Property(property="verification_token", type="string", example="abc123...", description="Verification token from verify-otp endpoint (required for API)"),
     *             @OA\Property(property="token", type="string", example="abc123...", description="Password reset token (required for Web)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Your password has been reset successfully!")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error or invalid verification token"),
     *     @OA\Response(response=400, description="Invalid or expired verification token")
     * )
     */
    public function reset(Request $request)
    {
        // For API requests, use verification token (from verify-otp endpoint)
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->resetPasswordWithOtp($request);
        }

        // For web requests, use the standard token-based reset
        // Use the password broker to handle web reset
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $response = $this->broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $response == Password::PASSWORD_RESET
                    ? $this->sendResetResponse($request, $response)
                    : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Reset password using verification token (API only)
     */
    protected function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'verification_token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->respondError('User not found.', 404);
        }

        // Get the stored verification token from password_resets table
        $passwordReset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return $this->respondError('Verification token not found. Please verify OTP again.', 400);
        }

        // Check if verification token is expired (10 minutes from OTP verification)
        if (now()->diffInMinutes($passwordReset->created_at) > 10) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return $this->respondError('Verification token has expired. Please verify OTP again.', 400);
        }

        // Verify the verification token
        if (!Hash::check($request->verification_token, $passwordReset->token)) {
            return $this->respondError('Invalid verification token. Please verify OTP again.', 422);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the used verification token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return $this->respond([
            'message' => 'Your password has been reset successfully!'
        ]);
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->respond([
                'message' => trans($response)
            ]);
        }

        return redirect('/password/reset?email=' . urlencode($request->email))
            ->with('success', trans($response));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->respondError(trans($response), 422);
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }

    public function showResetForm(Request $request, $token = null)
    {
        $pageConfigs = [
            'bodyClass' => "bg-full-screen-image",
            'blankPage' => true
        ];
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email, 'pageConfigs' => $pageConfigs]
        );
    }
}
