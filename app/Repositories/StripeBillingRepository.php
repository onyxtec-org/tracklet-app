<?php

namespace App\Repositories;

use App\Contracts\BillingRepositoryInterface;
use Stripe\Stripe;
use Stripe\PaymentMethod;
use Stripe\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class StripeBillingRepository implements BillingRepositoryInterface {

    public function __construct($stripeApiKey) {
        Stripe::setApiKey($stripeApiKey);
    }

    public function createCustomer($data) {
        $user = User::create($data->except('_token'));
        $user->createAsStripeCustomer();
    }

    public function addPaymentMethod($data) {
        try {
            // Create or get the Stripe customer
            $data['user']->createOrGetStripeCustomer();
        
            // Attach the payment method to the customer (user)
            $data['user']->addPaymentMethod($data['paymentMethodId']);
        
            // Set the payment method as the default for the customer
            $data['user']->updateDefaultPaymentMethod($data['paymentMethodId']);
        
            // Retrieve the default payment method from Stripe
            $stripePaymentMethodId = $data['user']->defaultPaymentMethod()->id;
            $stripePaymentMethod = PaymentMethod::retrieve($stripePaymentMethodId);
            $stripePaymentMethod->attach(['customer' => $data['user']->stripe_id]);
        
            // Update the billing details
            $stripePaymentMethod->billing_details = ['name' => $data['cardholderName']];
            $stripePaymentMethod->save();
        
            // Handle success and return a response
            return [
                'success' => true,
                'message' => 'Payment method added and updated successfully.',
            ];
        } catch (\Exception $e) {
            // Handle errors and return an error response
            Log::channel('stripeLog')->error('Something went wrong => : '.$e->getMessage());
        
            return [
                'success' => false,
                'message' => 'Something went wrong!',
            ];
        }
    }
}
