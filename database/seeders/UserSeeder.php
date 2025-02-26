<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Create Admin User
        $admin = User::firstOrCreate([
            'email' => 'admin@truview.com'
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('asdfasdf')
        ]);

        // Assign Admin Role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }

        // Create Regular User
        $user = User::firstOrCreate([
            'email' => 'user@truview.com'
        ], [
            'name' => 'Regular User',
            'password' => Hash::make('asdfasdf')
        ]);

        // Assign User Role
        if (!$user->hasRole('user')) {
            $user->assignRole($userRole);
        }
    }
}


