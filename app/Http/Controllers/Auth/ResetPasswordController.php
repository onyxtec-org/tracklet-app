<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

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

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        if ($request->expectsJson()) {
            return new \Illuminate\Http\JsonResponse(['message' => trans($response)], 200);
        }

        return redirect('/password/reset?email=' . urlencode($request->email))
            ->with('success', trans($response));
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
