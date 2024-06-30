<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeController;
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

Route::group(['middleware' => 'auth'], function () {
    //Dashboard Routes
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/password', [ProfileController::class, 'updatePasswordProfile'])->name('password.profile.update');

    // Stripe routes
    Route::post('/stripe/create/customer',[StripeController::class,'createCustomer'])->name('stripe.create.customer');
    Route::post('/stripe/add-payment-method', [StripeController::class,'addPaymentMethod'])->name('addPaymentMethod');
});
