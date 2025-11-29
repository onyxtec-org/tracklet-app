<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrganizationInvitationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SuperAdmin\OrganizationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LegalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth routes
Auth::routes();

// Public legal pages
Route::get('/terms', [LegalController::class, 'terms'])
    ->name('legal.terms');
Route::get('/privacy', [LegalController::class, 'privacy'])
    ->name('legal.privacy');

// Public organization registration routes
Route::get('/register-organization', [\App\Http\Controllers\OrganizationRegistrationController::class, 'show'])
    ->name('organization.register.show')
    ->middleware('guest');
Route::post('/register-organization', [\App\Http\Controllers\OrganizationRegistrationController::class, 'register'])
    ->name('organization.register')
    ->middleware('guest');

// Public invitation routes
Route::get('/invitation/{token}', [OrganizationInvitationController::class, 'show'])
    ->name('organization.invitation.show');
Route::post('/invitation/{token}/accept', [OrganizationInvitationController::class, 'accept'])
    ->name('organization.invitation.accept');

// Stripe webhook (must be outside auth middleware)
Route::post('/webhook/stripe', [SubscriptionController::class, 'webhook'])
    ->name('webhook.stripe')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::group(['middleware' => 'auth'], function () {
    // Password change route (must be accessible even if password change required)
    Route::get('/password/change', [AuthController::class, 'showChangePasswordForm'])
        ->name('password.change')
        ->withoutMiddleware(['require.password.change']);
    Route::post('/password/change', [AuthController::class, 'changePassword'])
        ->name('password.change.submit')
        ->withoutMiddleware(['require.password.change']);
});

Route::group(['middleware' => ['auth', 'require.password.change']], function () {
    // Subscription Routes (accessible without subscription)
    Route::get('/subscription/checkout', [SubscriptionController::class, 'checkout'])
        ->name('subscription.checkout');
    Route::post('/subscription/checkout', [SubscriptionController::class, 'createCheckoutSession'])
        ->name('subscription.checkout.create');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])
        ->name('subscription.success');
    
    // Profile Routes (accessible without subscription)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePasswordProfile'])->name('password.profile.update');
    
    // Dashboard Routes (requires subscription, except for super admin)
    Route::get('/', [DashboardController::class, 'index'])
        ->middleware('subscribed')
        ->name('dashboard.index');
    
    // Super Admin Routes
    Route::group(['middleware' => 'role:super_admin', 'prefix' => 'super-admin', 'as' => 'superadmin.'], function () {
        Route::resource('organizations', OrganizationController::class);
        Route::post('organizations/{organization}/resend-invitation', [OrganizationController::class, 'resendInvitation'])
            ->name('organizations.resend-invitation');
    });
    
    // Organization routes (require subscription)
    Route::group(['middleware' => ['subscribed', 'organization']], function () {
        
        // User Management Module (Admin role only)
        Route::group(['middleware' => 'role:admin', 'prefix' => 'users', 'as' => 'users.'], function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('{user}', [UserController::class, 'show'])->name('show');
            Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('{user}', [UserController::class, 'update'])->name('update');
            Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
        });
        
        // Expense Tracking Module (Finance role)
        Route::group(['middleware' => 'role_or_permission:admin|finance', 'prefix' => 'expenses', 'as' => 'expenses.'], function () {
            Route::resource('categories', ExpenseCategoryController::class)->except(['show']);
            Route::get('/', [ExpenseController::class, 'index'])->name('index');
            Route::get('create', [ExpenseController::class, 'create'])->name('create');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
            // Specific routes must come before resource routes to avoid route conflicts
            Route::get('reports', [ExpenseController::class, 'reports'])->name('reports');
            Route::get('charts', [ExpenseController::class, 'charts'])->name('charts');
            Route::get('export', [ExpenseController::class, 'export'])->name('export');
            // Approval routes (Admin only)
            Route::post('{expense}/approve', [ExpenseController::class, 'approve'])->name('approve')->middleware('role:admin');
            Route::post('{expense}/reject', [ExpenseController::class, 'reject'])->name('reject')->middleware('role:admin');
            // Resource routes
            Route::get('{expense}', [ExpenseController::class, 'show'])->name('show');
            Route::get('{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
            Route::put('{expense}', [ExpenseController::class, 'update'])->name('update');
            Route::delete('{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        });
        
        // Inventory Management Module (Admin Support role)
        Route::group(['middleware' => 'role_or_permission:admin|admin_support', 'prefix' => 'inventory', 'as' => 'inventory.'], function () {
            Route::resource('items', InventoryController::class);
            Route::post('items/{inventoryItem}/stock', [InventoryController::class, 'logStock'])->name('items.stock');
            Route::get('items/{inventoryItem}/transactions', [InventoryController::class, 'stockTransactions'])->name('items.transactions');
            Route::get('low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
            Route::get('purchase-history', [InventoryController::class, 'purchaseHistory'])->name('purchase-history');
            Route::get('aging-report', [InventoryController::class, 'agingReport'])->name('aging-report');
        });
        
        // Asset Management Module (Admin Support role)
        Route::group(['middleware' => 'role_or_permission:admin|admin_support', 'prefix' => 'assets', 'as' => 'assets.'], function () {
            Route::get('/', [AssetController::class, 'index'])->name('index');
            Route::get('create', [AssetController::class, 'create'])->name('create');
            Route::post('/', [AssetController::class, 'store'])->name('store');
            Route::get('{asset}', [AssetController::class, 'show'])->name('show');
            Route::get('{asset}/edit', [AssetController::class, 'edit'])->name('edit');
            Route::put('{asset}', [AssetController::class, 'update'])->name('update');
            Route::delete('{asset}', [AssetController::class, 'destroy'])->name('destroy');
            Route::post('{asset}/movement', [AssetController::class, 'logMovement'])->name('movement');
        });
        
        // Maintenance Module (Admin Support role)
        Route::group(['middleware' => 'role_or_permission:admin|admin_support', 'prefix' => 'maintenance', 'as' => 'maintenance.'], function () {
            Route::get('/', [MaintenanceController::class, 'index'])->name('index');
            Route::get('create', [MaintenanceController::class, 'create'])->name('create');
            Route::post('/', [MaintenanceController::class, 'store'])->name('store');
            // Specific routes must come before resource routes to avoid route conflicts
            Route::get('upcoming', [MaintenanceController::class, 'upcoming'])->name('upcoming');
            // Resource routes
            Route::get('{maintenanceRecord}', [MaintenanceController::class, 'show'])->name('show');
            Route::get('{maintenanceRecord}/edit', [MaintenanceController::class, 'edit'])->name('edit');
            Route::put('{maintenanceRecord}', [MaintenanceController::class, 'update'])->name('update');
            Route::delete('{maintenanceRecord}', [MaintenanceController::class, 'destroy'])->name('destroy');
        });
        
        // General Staff - Read-only access
        Route::group(['middleware' => 'role:general_staff', 'prefix' => 'view', 'as' => 'view.'], function () {
            // Assets - Read-only (only their own assigned assets)
            Route::get('assets', [AssetController::class, 'index'])->name('assets');
            Route::get('assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
        });
    });
});
