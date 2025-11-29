<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\MaintenanceRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class DemoOrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating demo organization with sample data...');

        // Create demo organization (NOT subscribed)
        $organization = Organization::firstOrCreate(
            ['slug' => 'demo-company'],
            [
                'name' => 'Demo Company Inc.',
                'email' => 'admin@democompany.com',
                'is_subscribed' => false,
                'is_active' => true,
                'registration_source' => 'self_registered',
            ]
        );

        $this->command->info("Created organization: {$organization->name}");

        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $financeRole = Role::where('name', 'finance')->first();
        $adminSupportRole = Role::where('name', 'admin_support')->first();
        $generalStaffRole = Role::where('name', 'general_staff')->first();

        // Create admin user (password: password)
        $admin = User::firstOrCreate(
            ['email' => 'admin@democompany.com'],
            [
                'name' => 'John Admin',
                'password' => Hash::make('password'),
                'organization_id' => $organization->id,
                'email_verified_at' => now(),
                'must_change_password' => false,
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }
        $this->command->info("Created admin user: {$admin->email} (password: password)");

        // Create finance user (password: password)
        $finance = User::firstOrCreate(
            ['email' => 'finance@democompany.com'],
            [
                'name' => 'Sarah Finance',
                'password' => Hash::make('password'),
                'organization_id' => $organization->id,
                'email_verified_at' => now(),
                'must_change_password' => false,
            ]
        );
        if (!$finance->hasRole('finance')) {
            $finance->assignRole($financeRole);
        }
        $this->command->info("Created finance user: {$finance->email} (password: password)");

        // Create admin support user (password: password)
        $adminSupport = User::firstOrCreate(
            ['email' => 'support@democompany.com'],
            [
                'name' => 'Mike Support',
                'password' => Hash::make('password'),
                'organization_id' => $organization->id,
                'email_verified_at' => now(),
                'must_change_password' => false,
            ]
        );
        if (!$adminSupport->hasRole('admin_support')) {
            $adminSupport->assignRole($adminSupportRole);
        }
        $this->command->info("Created admin support user: {$adminSupport->email} (password: password)");

        // Create general staff user (password: password)
        $staff = User::firstOrCreate(
            ['email' => 'staff@democompany.com'],
            [
                'name' => 'Emily Staff',
                'password' => Hash::make('password'),
                'organization_id' => $organization->id,
                'email_verified_at' => now(),
                'must_change_password' => false,
            ]
        );
        if (!$staff->hasRole('general_staff')) {
            $staff->assignRole($generalStaffRole);
        }
        $this->command->info("Created general staff user: {$staff->email} (password: password)");

        // Seed expense categories
        $this->seedExpenseCategories($organization);
        
        // Seed expenses
        $this->seedExpenses($organization, $admin, $finance);
        
        // Seed inventory items
        $this->seedInventoryItems($organization, $adminSupport);
        
        // Seed assets
        $this->seedAssets($organization, $admin, $adminSupport);
        
        // Seed maintenance records
        $this->seedMaintenanceRecords($organization, $adminSupport);

        $this->command->info('✅ Demo organization created successfully!');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('  Admin: admin@democompany.com / password');
        $this->command->info('  Finance: finance@democompany.com / password');
        $this->command->info('  Admin Support: support@democompany.com / password');
        $this->command->info('  General Staff: staff@democompany.com / password');
        $this->command->info('');
        $this->command->info('Note: Organization is NOT subscribed. You can login and subscribe to test the subscription flow.');
    }

    /**
     * Seed expense categories
     */
    private function seedExpenseCategories(Organization $organization): void
    {
        $categories = [
            'Utilities' => 'Electricity, water, gas, internet, phone bills',
            'Stationery' => 'Office supplies, paper, pens, etc.',
            'Salaries' => 'Employee salaries and wages',
            'Repairs' => 'Equipment and facility repairs',
            'Subscriptions' => 'Software subscriptions, services',
            'Travel' => 'Business travel expenses',
            'Marketing' => 'Advertising and marketing expenses',
            'Professional Services' => 'Legal, accounting, consulting fees',
            'Equipment' => 'Office equipment purchases',
            'Other' => 'Miscellaneous expenses',
        ];

        foreach ($categories as $name => $description) {
            ExpenseCategory::firstOrCreate(
                [
                    'organization_id' => $organization->id,
                    'name' => $name,
                ],
                [
                    'description' => $description,
                    'is_system' => false,
                ]
            );
        }

        $this->command->info('  ✓ Created expense categories');
    }

    /**
     * Seed expenses
     */
    private function seedExpenses(Organization $organization, User $admin, User $finance): void
    {
        $categories = ExpenseCategory::where('organization_id', $organization->id)->get();
        $users = [$admin, $finance];
        
        // Create expenses for the last 3 months
        for ($month = 0; $month < 3; $month++) {
            $date = Carbon::now()->subMonths($month);
            
            // Create 5-10 expenses per month
            $expenseCount = rand(5, 10);
            
            for ($i = 0; $i < $expenseCount; $i++) {
                $expenseDate = $date->copy()->subDays(rand(0, 28));
                $category = $categories->random();
                $user = $users[array_rand($users)];
                
                Expense::create([
                    'organization_id' => $organization->id,
                    'expense_category_id' => $category->id,
                    'user_id' => $user->id,
                    'expense_date' => $expenseDate->format('Y-m-d'),
                    'amount' => rand(50, 5000) + (rand(0, 99) / 100), // Random amount with cents
                    'description' => $this->getExpenseDescription($category->name),
                    'vendor_payee' => $this->getVendorName(),
                ]);
            }
        }

        $this->command->info('  ✓ Created expenses (last 3 months)');
    }

    /**
     * Seed inventory items
     */
    private function seedInventoryItems(Organization $organization, User $adminSupport): void
    {
        $items = [
            ['name' => 'A4 Paper (Ream)', 'category' => 'Stationery', 'unit_price' => 5.50, 'stock' => 45, 'threshold' => 20],
            ['name' => 'Black Ink Cartridge', 'category' => 'Office Supplies', 'unit_price' => 25.00, 'stock' => 8, 'threshold' => 10],
            ['name' => 'Blue Pens (Box of 12)', 'category' => 'Stationery', 'unit_price' => 8.00, 'stock' => 15, 'threshold' => 20],
            ['name' => 'Stapler', 'category' => 'Office Supplies', 'unit_price' => 12.50, 'stock' => 5, 'threshold' => 5],
            ['name' => 'Coffee Beans (1kg)', 'category' => 'Consumables', 'unit_price' => 15.00, 'stock' => 12, 'threshold' => 10],
            ['name' => 'Printer Paper (500 sheets)', 'category' => 'Stationery', 'unit_price' => 8.50, 'stock' => 25, 'threshold' => 15],
            ['name' => 'Sticky Notes (Pack)', 'category' => 'Stationery', 'unit_price' => 3.00, 'stock' => 30, 'threshold' => 20],
            ['name' => 'Toner Cartridge', 'category' => 'Office Supplies', 'unit_price' => 45.00, 'stock' => 3, 'threshold' => 5],
            ['name' => 'Whiteboard Markers (Set)', 'category' => 'Office Supplies', 'unit_price' => 10.00, 'stock' => 7, 'threshold' => 10],
            ['name' => 'File Folders (Pack of 50)', 'category' => 'Stationery', 'unit_price' => 12.00, 'stock' => 18, 'threshold' => 15],
        ];

        foreach ($items as $itemData) {
            $item = InventoryItem::create([
                'organization_id' => $organization->id,
                'name' => $itemData['name'],
                'category' => $itemData['category'],
                'quantity' => $itemData['stock'],
                'unit_price' => $itemData['unit_price'],
                'total_price' => $itemData['unit_price'] * $itemData['stock'],
                'minimum_threshold' => $itemData['threshold'],
            ]);

            // Create some stock transactions
            // Initial stock in
            $transactionDate = Carbon::now()->subMonths(2);
            StockTransaction::create([
                'organization_id' => $organization->id,
                'inventory_item_id' => $item->id,
                'user_id' => $adminSupport->id,
                'type' => 'in',
                'quantity' => $itemData['stock'] + rand(10, 30), // More than current stock
                'reference' => 'Initial stock purchase',
                'transaction_date' => $transactionDate->format('Y-m-d'),
                'created_at' => $transactionDate,
            ]);

            // Some stock out transactions
            $stockOutCount = rand(2, 5);
            for ($i = 0; $i < $stockOutCount; $i++) {
                $outDate = Carbon::now()->subDays(rand(1, 60));
                StockTransaction::create([
                    'organization_id' => $organization->id,
                    'inventory_item_id' => $item->id,
                    'user_id' => $adminSupport->id,
                    'type' => 'out',
                    'quantity' => rand(1, 5),
                    'reference' => 'Office usage',
                    'transaction_date' => $outDate->format('Y-m-d'),
                    'created_at' => $outDate,
                ]);
            }
        }

        $this->command->info('  ✓ Created inventory items with stock transactions');
    }

    /**
     * Seed assets
     */
    private function seedAssets(Organization $organization, User $admin, User $adminSupport): void
    {
        $assets = [
            [
                'name' => 'Dell Laptop XPS 15',
                'category' => 'IT Equipment',
                'purchase_date' => Carbon::now()->subYears(2)->subMonths(3),
                'purchase_price' => 1299.99,
                'vendor' => 'Dell Technologies',
                'warranty_expiry' => Carbon::now()->addMonths(9),
                'assigned_to' => $admin->id,
                'location' => 'Office - Floor 2',
                'status' => 'active',
            ],
            [
                'name' => 'HP LaserJet Pro Printer',
                'category' => 'IT Equipment',
                'purchase_date' => Carbon::now()->subMonths(8),
                'purchase_price' => 450.00,
                'vendor' => 'HP Inc.',
                'warranty_expiry' => Carbon::now()->addMonths(4),
                'assigned_to' => null,
                'location' => 'Office - Reception',
                'status' => 'active',
            ],
            [
                'name' => 'Office Desk (Executive)',
                'category' => 'Furniture',
                'purchase_date' => Carbon::now()->subYears(1)->subMonths(6),
                'purchase_price' => 850.00,
                'vendor' => 'Office Furniture Co.',
                'warranty_expiry' => null,
                'assigned_to' => $admin->id,
                'location' => 'Office - Floor 2',
                'status' => 'active',
            ],
            [
                'name' => 'Ergonomic Office Chair',
                'category' => 'Furniture',
                'purchase_date' => Carbon::now()->subMonths(4),
                'purchase_price' => 350.00,
                'vendor' => 'Office Furniture Co.',
                'warranty_expiry' => Carbon::now()->addMonths(8),
                'assigned_to' => $adminSupport->id,
                'location' => 'Office - Floor 1',
                'status' => 'active',
            ],
            [
                'name' => 'MacBook Pro 16"',
                'category' => 'IT Equipment',
                'purchase_date' => Carbon::now()->subMonths(6),
                'purchase_price' => 2499.99,
                'vendor' => 'Apple Inc.',
                'warranty_expiry' => Carbon::now()->addMonths(6),
                'assigned_to' => $adminSupport->id,
                'location' => 'Office - Floor 1',
                'status' => 'active',
            ],
            [
                'name' => 'Air Conditioner Unit',
                'category' => 'Facilities',
                'purchase_date' => Carbon::now()->subYears(3),
                'purchase_price' => 1200.00,
                'vendor' => 'HVAC Solutions',
                'warranty_expiry' => Carbon::now()->subMonths(6), // Expired
                'assigned_to' => null,
                'location' => 'Office - Conference Room',
                'status' => 'in_repair',
            ],
            [
                'name' => 'Projector Epson',
                'category' => 'IT Equipment',
                'purchase_date' => Carbon::now()->subMonths(10),
                'purchase_price' => 650.00,
                'vendor' => 'Epson Corporation',
                'warranty_expiry' => Carbon::now()->addMonths(2),
                'assigned_to' => null,
                'location' => 'Office - Conference Room',
                'status' => 'active',
            ],
            [
                'name' => 'Old Desktop Computer',
                'category' => 'IT Equipment',
                'purchase_date' => Carbon::now()->subYears(5),
                'purchase_price' => 800.00,
                'vendor' => 'Dell Technologies',
                'warranty_expiry' => null,
                'assigned_to' => null,
                'location' => 'Storage Room',
                'status' => 'retired',
            ],
        ];

        foreach ($assets as $assetData) {
            $asset = Asset::create([
                'organization_id' => $organization->id,
                'asset_code' => Asset::generateAssetCode($organization),
                'name' => $assetData['name'],
                'category' => $assetData['category'],
                'purchase_date' => $assetData['purchase_date']->format('Y-m-d'),
                'purchase_price' => $assetData['purchase_price'],
                'vendor' => $assetData['vendor'],
                'warranty_expiry' => $assetData['warranty_expiry'] ? $assetData['warranty_expiry']->format('Y-m-d') : null,
                'assigned_to_user_id' => $assetData['assigned_to'],
                'assigned_to_location' => $assetData['location'],
                'status' => $assetData['status'],
                'status_changed_at' => $assetData['status'] !== 'active' ? Carbon::now()->subMonths(rand(1, 6))->format('Y-m-d') : null,
                'status_change_reason' => $assetData['status'] !== 'active' ? $this->getStatusReason($assetData['status']) : null,
            ]);

            // Create some asset movements
            if ($assetData['status'] === 'retired') {
                $movementDate = $asset->status_changed_at ? Carbon::parse($asset->status_changed_at) : Carbon::now()->subMonths(rand(1, 6));
                AssetMovement::create([
                    'organization_id' => $organization->id,
                    'asset_id' => $asset->id,
                    'user_id' => $adminSupport->id,
                    'movement_date' => $movementDate->format('Y-m-d'),
                    'movement_type' => 'location_change',
                    'from_location' => 'Office - Floor 2',
                    'to_location' => 'Storage Room',
                    'reason' => 'Asset retired and moved to storage',
                    'created_at' => $movementDate,
                ]);
            } elseif ($assetData['status'] === 'in_repair') {
                $repairDate = Carbon::now()->subDays(rand(1, 30));
                AssetMovement::create([
                    'organization_id' => $organization->id,
                    'asset_id' => $asset->id,
                    'user_id' => $adminSupport->id,
                    'movement_date' => $repairDate->format('Y-m-d'),
                    'movement_type' => 'location_change',
                    'from_location' => $assetData['location'],
                    'to_location' => 'Repair Shop',
                    'reason' => 'Sent for maintenance',
                    'created_at' => $repairDate,
                ]);
            }
        }

        $this->command->info('  ✓ Created assets with movements');
    }

    /**
     * Seed maintenance records
     */
    private function seedMaintenanceRecords(Organization $organization, User $adminSupport): void
    {
        $assets = Asset::where('organization_id', $organization->id)->get();
        
        $maintenanceTypes = ['Scheduled', 'Repair', 'Inspection', 'Other'];
        $statuses = ['scheduled', 'in_progress', 'completed', 'cancelled'];
        
        // Create past maintenance records (completed)
        foreach ($assets->take(5) as $asset) {
            MaintenanceRecord::create([
                'organization_id' => $organization->id,
                'asset_id' => $asset->id,
                'user_id' => $adminSupport->id,
                'type' => strtolower($maintenanceTypes[array_rand($maintenanceTypes)]),
                'scheduled_date' => Carbon::now()->subMonths(rand(1, 6))->format('Y-m-d'),
                'completed_date' => Carbon::now()->subMonths(rand(1, 5))->format('Y-m-d'),
                'status' => 'completed',
                'description' => 'Routine maintenance and inspection',
                'cost' => rand(50, 500) + (rand(0, 99) / 100),
                'notes' => 'Maintenance completed successfully',
            ]);
        }

        // Create upcoming maintenance (scheduled)
        foreach ($assets->take(3) as $asset) {
            MaintenanceRecord::create([
                'organization_id' => $organization->id,
                'asset_id' => $asset->id,
                'user_id' => $adminSupport->id,
                'type' => 'scheduled',
                'scheduled_date' => Carbon::now()->addDays(rand(1, 7))->format('Y-m-d'),
                'status' => 'pending',
                'description' => 'Regular scheduled maintenance',
                'cost' => null,
                'notes' => 'Scheduled maintenance appointment',
            ]);
        }

        // Create in-progress maintenance
        if ($assets->count() > 0) {
            $asset = $assets->where('status', 'in_repair')->first() ?? $assets->first();
            MaintenanceRecord::create([
                'organization_id' => $organization->id,
                'asset_id' => $asset->id,
                'user_id' => $adminSupport->id,
                'type' => 'repair',
                'scheduled_date' => Carbon::now()->subDays(rand(1, 10))->format('Y-m-d'),
                'status' => 'in_progress',
                'description' => 'Repair work in progress',
                'cost' => null,
                'notes' => 'Currently being repaired',
            ]);
        }

        $this->command->info('  ✓ Created maintenance records');
    }

    /**
     * Get expense description based on category
     */
    private function getExpenseDescription(string $category): string
    {
        $descriptions = [
            'Utilities' => ['Monthly electricity bill', 'Internet service fee', 'Water bill', 'Phone service'],
            'Stationery' => ['Office supplies purchase', 'Paper and printing materials', 'Writing instruments'],
            'Salaries' => ['Monthly payroll', 'Employee salary payment'],
            'Repairs' => ['Equipment repair service', 'Facility maintenance'],
            'Subscriptions' => ['Software subscription renewal', 'Service subscription'],
            'Travel' => ['Business trip expenses', 'Client meeting travel', 'Conference attendance'],
            'Marketing' => ['Online advertising campaign', 'Marketing materials', 'Social media promotion'],
            'Professional Services' => ['Legal consultation', 'Accounting services', 'Consulting fees'],
            'Equipment' => ['Office equipment purchase', 'IT equipment'],
            'Other' => ['Miscellaneous expense', 'General office expense'],
        ];

        $options = $descriptions[$category] ?? ['General expense'];
        return $options[array_rand($options)];
    }

    /**
     * Get vendor name
     */
    private function getVendorName(): string
    {
        $vendors = [
            'Office Supplies Co.',
            'Tech Solutions Inc.',
            'Service Provider Ltd.',
            'Business Services Corp.',
            'Professional Services Group',
            'Utility Company',
            'Equipment Supplier',
            'Marketing Agency',
        ];

        return $vendors[array_rand($vendors)];
    }

    /**
     * Get status reason
     */
    private function getStatusReason(string $status): string
    {
        $reasons = [
            'in_repair' => 'Requires maintenance and repair',
            'retired' => 'End of useful life, replaced with new equipment',
        ];

        return $reasons[$status] ?? 'Status changed';
    }
}
