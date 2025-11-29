<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrganizationInvitationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SuperAdmin\OrganizationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API routes - Authentication
Route::post('/register', [AuthController::class, 'register'])
    ->name('api.register');
Route::post('/login', [AuthController::class, 'login'])
    ->name('api.login');

// Public API routes
Route::get('/register-organization', [\App\Http\Controllers\OrganizationRegistrationController::class, 'show'])
    ->name('api.organization.register.show');
Route::post('/register-organization', [\App\Http\Controllers\OrganizationRegistrationController::class, 'register'])
    ->name('api.organization.register');

Route::get('/invitation/{token}', [OrganizationInvitationController::class, 'show'])
    ->name('api.organization.invitation.show');
Route::post('/invitation/{token}/accept', [OrganizationInvitationController::class, 'accept'])
    ->name('api.organization.invitation.accept');

// Stripe webhook (must be accessible without auth)
Route::post('/webhook/stripe', [SubscriptionController::class, 'webhook'])
    ->name('api.webhook.stripe');

// Authenticated API routes
// Note: 'auth:sanctum' middleware works with Bearer tokens for API requests
Route::middleware('auth:sanctum')->group(function () {
    // Password change route (must be accessible even if password change required)
    Route::post('/change-password', [AuthController::class, 'changePassword'])
        ->name('api.change-password')
        ->withoutMiddleware(['require.password.change']);
});

Route::middleware(['auth:sanctum', 'require.password.change'])->group(function () {
    Route::get('/user', [AuthController::class, 'user'])
        ->name('api.user');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('api.logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('api.dashboard.index');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('api.profile');
    Route::put('/profile/password', [ProfileController::class, 'updatePasswordProfile'])
        ->name('api.password.profile.update');
    
    // Subscription
    Route::get('/subscription/checkout', [SubscriptionController::class, 'checkout'])
        ->name('api.subscription.checkout');
    Route::post('/subscription/checkout', [SubscriptionController::class, 'createCheckoutSession'])
        ->name('api.subscription.checkout.create');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])
        ->name('api.subscription.success');
    
    // Super Admin API routes
    Route::middleware('role:super_admin')->prefix('super-admin')->as('api.superadmin.')->group(function () {
        Route::apiResource('organizations', OrganizationController::class);
        Route::post('organizations/{organization}/resend-invitation', [OrganizationController::class, 'resendInvitation'])
            ->name('organizations.resend-invitation');
    });
    
    // Roles API (Available to all authenticated users)
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    
    // Organization API routes (require subscription)
    Route::middleware(['subscribed', 'organization'])->group(function () {
        
        // User Management API (Admin role only)
        Route::middleware('role:admin')->prefix('users')->as('api.users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('{user}', [UserController::class, 'show'])->name('show');
            Route::put('{user}', [UserController::class, 'update'])->name('update');
            Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
        });
        
        // Expense Tracking API (Finance role)
        Route::middleware('role_or_permission:admin|finance')->prefix('expenses')->as('api.expenses.')->group(function () {
            // Categories
            Route::get('categories', [ExpenseCategoryController::class, 'index'])->name('categories.index');
            Route::post('categories', [ExpenseCategoryController::class, 'store'])->name('categories.store');
            Route::put('categories/{expenseCategory}', [ExpenseCategoryController::class, 'update'])->name('categories.update');
            Route::delete('categories/{expenseCategory}', [ExpenseCategoryController::class, 'destroy'])->name('categories.destroy');
            
            // Expenses - Specific routes must come before resource routes
            Route::get('reports', [ExpenseController::class, 'reports'])->name('reports');
            Route::get('charts', [ExpenseController::class, 'charts'])->name('charts');
            Route::get('export', [ExpenseController::class, 'export'])->name('export');
            
            // Resource routes
            Route::get('/', [ExpenseController::class, 'index'])->name('index');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
            // Approval routes (Admin only) - must come before {expense} route
            Route::post('{expense}/approve', [ExpenseController::class, 'approve'])->name('approve')->middleware('role:admin');
            Route::post('{expense}/reject', [ExpenseController::class, 'reject'])->name('reject')->middleware('role:admin');
            Route::get('{expense}', [ExpenseController::class, 'show'])->name('show');
            Route::put('{expense}', [ExpenseController::class, 'update'])->name('update');
            Route::delete('{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        });
        
        // Inventory Management API (Admin Support role)
        Route::middleware('role_or_permission:admin|admin_support')->prefix('inventory')->as('api.inventory.')->group(function () {
            Route::get('items', [InventoryController::class, 'index'])->name('items.index');
            Route::post('items', [InventoryController::class, 'store'])->name('items.store');
            Route::get('items/{inventoryItem}', [InventoryController::class, 'show'])->name('items.show');
            Route::put('items/{inventoryItem}', [InventoryController::class, 'update'])->name('items.update');
            Route::delete('items/{inventoryItem}', [InventoryController::class, 'destroy'])->name('items.destroy');
            Route::post('items/{inventoryItem}/stock', [InventoryController::class, 'logStock'])->name('items.stock');
            Route::get('items/{inventoryItem}/transactions', [InventoryController::class, 'stockTransactions'])->name('items.transactions');
            Route::get('low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
            Route::get('purchase-history', [InventoryController::class, 'purchaseHistory'])->name('purchase-history');
            Route::get('aging-report', [InventoryController::class, 'agingReport'])->name('aging-report');
        });
        
        // Asset Management API (Admin Support role)
        Route::middleware('role_or_permission:admin|admin_support')->prefix('assets')->as('api.assets.')->group(function () {
            Route::get('/', [AssetController::class, 'index'])->name('index');
            Route::post('/', [AssetController::class, 'store'])->name('store');
            Route::get('{asset}', [AssetController::class, 'show'])->name('show');
            Route::put('{asset}', [AssetController::class, 'update'])->name('update');
            Route::delete('{asset}', [AssetController::class, 'destroy'])->name('destroy');
            Route::post('{asset}/movement', [AssetController::class, 'logMovement'])->name('movement');
        });
        
        // Maintenance API (Admin Support role)
        Route::middleware('role_or_permission:admin|admin_support')->prefix('maintenance')->as('api.maintenance.')->group(function () {
            Route::get('/', [MaintenanceController::class, 'index'])->name('index');
            Route::post('/', [MaintenanceController::class, 'store'])->name('store');
            // Specific routes must come before resource routes
            Route::get('upcoming', [MaintenanceController::class, 'upcoming'])->name('upcoming');
            // Resource routes
            Route::get('{maintenanceRecord}', [MaintenanceController::class, 'show'])->name('show');
            Route::put('{maintenanceRecord}', [MaintenanceController::class, 'update'])->name('update');
            Route::delete('{maintenanceRecord}', [MaintenanceController::class, 'destroy'])->name('destroy');
        });
        
        // General Staff - Read-only API access
        Route::middleware('role:general_staff')->prefix('view')->as('api.view.')->group(function () {
            Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses');
            Route::get('inventory', [InventoryController::class, 'index'])->name('inventory');
            Route::get('assets', [AssetController::class, 'index'])->name('assets');
        });
    });
});
