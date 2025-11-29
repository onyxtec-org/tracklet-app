<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails, ApiResponse;

    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     summary="Send password reset OTP (API) or reset link (Web)",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="User's email address")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully (API) or reset link sent (Web)",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="We have sent an OTP to your email address. Please check your inbox.")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // For API requests, send OTP instead of reset link
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->sendOtp($request);
        }

        // For web requests, use the standard reset link flow
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Send OTP for password reset (API only)
     */
    protected function sendOtp(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->respondError('We can\'t find a user with that email address.', 404);
        }

        // Generate 6-digit OTP
        $otp = str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in password_resets table (expires in 10 minutes)
        // Store OTP hash and mark it as unverified
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp) . '|unverified', // Mark as unverified
                'created_at' => now(),
            ]
        );

        // Send OTP via email
        try {
            Mail::to($user->email)->send(new PasswordResetOtpMail($otp, $user));
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
            return $this->respondError('Failed to send OTP. Please try again later.', 500);
        }

        return $this->respond([
            'message' => 'We have sent an OTP to your email address. Please check your inbox.'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/verify-otp",
     *     summary="Verify OTP for password reset",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="User's email address"),
     *             @OA\Property(property="otp", type="string", example="123456", description="6-digit OTP code received via email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP verified successfully. You can now reset your password."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="verification_token", type="string", example="abc123...", description="Token to use for password reset")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Invalid OTP"),
     *     @OA\Response(response=400, description="OTP expired or not found"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->respondError('User not found.', 404);
        }

        // Get the stored OTP from password_resets table
        $passwordReset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return $this->respondError('OTP not found. Please request a new OTP.', 400);
        }

        // Check if OTP is expired (10 minutes)
        if (now()->diffInMinutes($passwordReset->created_at) > 10) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return $this->respondError('OTP has expired. Please request a new OTP.', 400);
        }

        // Check if OTP is already verified
        if (strpos($passwordReset->token, '|unverified') === false) {
            return $this->respondError('OTP has already been used. Please request a new OTP.', 400);
        }

        // Extract the OTP hash (remove |unverified suffix)
        $otpHash = str_replace('|unverified', '', $passwordReset->token);

        // Verify OTP
        if (!Hash::check($request->otp, $otpHash)) {
            return $this->respondError('Invalid OTP. Please check and try again.', 422);
        }

        // Generate verification token for password reset
        $verificationToken = Str::random(64);

        // Store verification token (mark as verified by removing |unverified)
        DB::table('password_resets')->where('email', $request->email)->update([
            'token' => Hash::make($verificationToken),
            'created_at' => now(), // Reset expiration time
        ]);

        return $this->respond([
            'message' => 'OTP verified successfully. You can now reset your password.',
            'verification_token' => $verificationToken,
        ]);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->respond([
                'message' => trans($response)
            ]);
        }

        return back()
            ->with('success', trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->respondError(trans($response), 422);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }

    public function showLinkRequestForm()
    {
        $pageConfigs = [
            'bodyClass' => "bg-full-screen-image",
            'blankPage' => true
        ];

        return view('/auth/passwords/email', [
            'pageConfigs' => $pageConfigs
        ]);
    }
}
