<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\BillingRepositoryInterface;
use App\Repositories\StripeBillingRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BillingRepositoryInterface::class, function ($app) {
            // Retrieve the Stripe API key from the .env file
            $stripeApiKey = env('STRIPE_SECRET');
        
            // Inject the Stripe API key into the StripeBillingRepository constructor
            return new StripeBillingRepository($stripeApiKey);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
