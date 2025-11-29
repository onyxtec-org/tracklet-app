<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Traits\ApiResponse;

class ProfileController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $user = auth()->user();
        
        return $this->respond(
            ['user' => $user],
            'profile',
            ['user' => $user]
        );
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user->name = $validated['name'];

        if ($user->save()) {
            return $this->respond([
                'message' => 'Profile updated successfully',
                'user' => $user->fresh()
            ]);
        } else {
            return $this->respondError('Error while updating profile', 500);
        }
    }

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
