<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile');
    }
    public function updatePasswordProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
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

        $user->password = Hash::make($request->input('new-password'));

        if ($user->save()) {
            return redirect()->route('profile')
                ->with('success', 'Password updated successfully');
        } else {
            return redirect()->back()
                ->with('error', 'Error while updating password');
        }
    }
}
