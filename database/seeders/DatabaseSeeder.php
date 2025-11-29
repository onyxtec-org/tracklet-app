<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed roles first
        $this->call([
            RoleSeeder::class,
            SuperAdminSeeder::class,
            ExpenseCategorySeeder::class,
            DemoOrganizationSeeder::class,
        ]);
    }
}
