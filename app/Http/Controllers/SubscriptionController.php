<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription;

class SubscriptionController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/subscription/checkout",
     *     summary="Get checkout page",
     *     tags={"Subscription"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Checkout page data")
     * )
     */
    public function checkout()
    {
        $user = auth()->user();

        if (!$user || !$user->organization) {
            return $this->respondError('You must belong to an organization.', 403);
        }

        $organization = $user->organization;

        // If already subscribed, redirect to dashboard
        if ($organization->isSubscribed()) {
            return $this->respond([
                'message' => 'Your organization is already subscribed.',
                'redirect' => route('dashboard.index'),
            ]);
        }

        // Get pricing information from Stripe
        $priceInfo = null;
        $priceId = config('services.stripe.price_id');
        
        if ($priceId) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $price = \Stripe\Price::retrieve($priceId);
                
                // Calculate monthly and annual prices
                $amount = $price->unit_amount / 100; // Convert from cents
                $currency = strtoupper($price->currency);
                $interval = $price->recurring->interval ?? 'year';
                $intervalCount = $price->recurring->interval_count ?? 1;
                
                // Calculate monthly equivalent for annual plans
                $monthlyPrice = $interval === 'year' ? round($amount / 12, 2) : $amount;
                $annualPrice = $interval === 'year' ? $amount : ($amount * 12);
                
                $priceInfo = [
                    'amount' => $amount,
                    'monthly_price' => $monthlyPrice,
                    'annual_price' => $annualPrice,
                    'currency' => $currency,
                    'currency_symbol' => $currency === 'USD' ? '$' : $currency,
                    'interval' => $interval,
                    'interval_count' => $intervalCount,
                    'formatted_monthly' => $currency === 'USD' ? '$' . number_format($monthlyPrice, 2) : number_format($monthlyPrice, 2) . ' ' . $currency,
                    'formatted_annual' => $currency === 'USD' ? '$' . number_format($annualPrice, 2) : number_format($annualPrice, 2) . ' ' . $currency,
                ];
            } catch (\Exception $e) {
                Log::error('Failed to fetch Stripe price: ' . $e->getMessage());
                // Continue without price info - will show default
            }
        }

        return $this->respond(
            ['organization' => $organization, 'price_info' => $priceInfo],
            'subscription.checkout',
            ['organization' => $organization, 'priceInfo' => $priceInfo]
        );
    }

    /**
     * @OA\Post(
     *     path="/api/subscription/checkout",
     *     summary="Create Stripe checkout session",
     *     tags={"Subscription"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="price_id", type="string", example="price_xxxxx")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Checkout session created", @OA\JsonContent(@OA\Property(property="checkout_url", type="string")))
     * )
     */
    public function createCheckoutSession(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->organization) {
            return $this->respondError('You must belong to an organization.', 403);
        }

        $organization = $user->organization;

        // Get price ID from config or request
        $priceId = $request->input('price_id', config('services.stripe.price_id'));

        if (!$priceId) {
            return $this->respondError('Subscription price not configured.', 500);
        }

        try {
            // Use the admin user for Stripe customer (Cashier works with User model)
            $adminUser = $organization->admin() ?? $user;

            // Create Stripe customer if not exists
            if (!$adminUser->stripe_id) {
                $adminUser->createAsStripeCustomer([
                    'email' => $organization->email ?? $adminUser->email,
                    'name' => $organization->name,
                    'metadata' => [
                        'organization_id' => $organization->id,
                    ],
                ]);
            }

            // Store organization ID in user metadata for webhook handling
            $adminUser->updateStripeCustomer([
                'metadata' => [
                    'organization_id' => $organization->id,
                ],
            ]);

            // Create checkout session using Stripe directly
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            // Set trial period to 1 month (30 days) for yearly subscriptions
            $trialPeriodDays = 30;

            $checkoutSession = \Stripe\Checkout\Session::create([
                'customer' => $adminUser->stripe_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'subscription_data' => [
                    'trial_period_days' => $trialPeriodDays,
                    'metadata' => [
                        'organization_id' => $organization->id,
                    ],
                ],
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.checkout'),
                'metadata' => [
                    'organization_id' => $organization->id,
                    'user_id' => $adminUser->id,
                ],
            ]);

            return $this->respond([
                'checkout_url' => $checkoutSession->url,
                'session_id' => $checkoutSession->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());
            return $this->respondError('Failed to create checkout session: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Handle successful subscription
     * 
     * Note: This is a fallback. The webhook should handle subscription updates automatically.
     * This endpoint provides immediate feedback to the user.
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return $this->respondError('Invalid session.', 400);
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if ($session->payment_status === 'paid' && $session->subscription) {
                // Get subscription details
                $subscription = \Stripe\Subscription::retrieve($session->subscription);
                $organizationId = $session->metadata->organization_id ?? $subscription->metadata->organization_id ?? null;
                
                if ($organizationId) {
                    $organization = Organization::find($organizationId);
                    
                    if ($organization) {
                        // Check if subscription is in trial period
                        $isTrial = $subscription->status === 'trialing' && $subscription->trial_end;
                        $trialEndsAt = $isTrial ? \Carbon\Carbon::createFromTimestamp($subscription->trial_end) : null;
                        
                        // Update organization subscription status
                        // Note: Webhook will also update this, but we do it here for immediate feedback
                        $organization->update([
                            'is_subscribed' => true,
                            'subscription_ends_at' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_end),
                            'trial_ends_at' => $trialEndsAt,
                            'stripe_id' => $subscription->customer,
                        ]);

                        $message = $isTrial 
                            ? 'Your subscription is active! You\'re now on a 1-month free trial. Enjoy full access to Tracklet!'
                            : 'Subscription activated successfully!';

                        return $this->respond([
                            'message' => $message,
                            'redirect' => route('dashboard.index'),
                            'organization' => $organization,
                            'is_trial' => $isTrial,
                        ]);
                    }
                }
            }

            // If payment not completed, still return success page but with message
            return $this->respond([
                'message' => 'Payment processing. Your subscription will be activated shortly.',
                'redirect' => route('dashboard.index'),
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription Success Error: ' . $e->getMessage());
            // Don't fail completely - webhook will handle it
            return $this->respond([
                'message' => 'Payment received. Your subscription will be activated shortly.',
                'redirect' => route('dashboard.index'),
            ]);
        }
    }

    /**
     * Handle Stripe webhook
     * 
     * This endpoint handles all Stripe webhook events to keep the site in sync.
     * It's accessible via both web and API routes.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        // Verify webhook signature
        if (!$endpointSecret) {
            Log::error('Stripe Webhook Secret not configured');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Stripe Webhook Invalid Payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Stripe Webhook Invalid Signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }

        // Log webhook received
        Log::info('Stripe Webhook Received: ' . $event->type, [
            'event_id' => $event->id,
            'event_type' => $event->type,
        ]);

        // Handle the event
        try {
            switch ($event->type) {
                // Subscription Events
                case 'customer.subscription.created':
                case 'customer.subscription.updated':
                    $subscription = $event->data->object;
                    $this->handleSubscriptionUpdate($subscription);
                    break;

                case 'customer.subscription.deleted':
                    $subscription = $event->data->object;
                    $this->handleSubscriptionCancellation($subscription);
                    break;

                case 'customer.subscription.trial_will_end':
                    $subscription = $event->data->object;
                    $this->handleTrialWillEnd($subscription);
                    break;

                // Invoice Events
                case 'invoice.payment_succeeded':
                    $invoice = $event->data->object;
                    $this->handlePaymentSucceeded($invoice);
                    break;

                case 'invoice.payment_failed':
                    $invoice = $event->data->object;
                    $this->handlePaymentFailed($invoice);
                    break;

                case 'invoice.payment_action_required':
                    $invoice = $event->data->object;
                    $this->handlePaymentActionRequired($invoice);
                    break;

                case 'invoice.upcoming':
                    $invoice = $event->data->object;
                    $this->handleInvoiceUpcoming($invoice);
                    break;

                // Customer Events
                case 'customer.updated':
                    $customer = $event->data->object;
                    $this->handleCustomerUpdate($customer);
                    break;

                case 'customer.deleted':
                    $customer = $event->data->object;
                    $this->handleCustomerDeleted($customer);
                    break;

                // Payment Method Events
                case 'payment_method.attached':
                    $paymentMethod = $event->data->object;
                    $this->handlePaymentMethodAttached($paymentMethod);
                    break;

                default:
                    Log::info('Unhandled Stripe Webhook Event: ' . $event->type);
            }

            return response()->json(['received' => true, 'event_type' => $event->type], 200);

        } catch (\Exception $e) {
            Log::error('Error processing Stripe webhook: ' . $e->getMessage(), [
                'event_type' => $event->type,
                'event_id' => $event->id,
                'trace' => $e->getTraceAsString(),
            ]);
            // Return 200 to prevent Stripe from retrying (we'll handle manually)
            return response()->json(['error' => 'Processing failed', 'event_type' => $event->type], 200);
        }
    }

    protected function handleSubscriptionUpdate($subscription)
    {
        // Get organization from subscription metadata
        $organizationId = $subscription->metadata->organization_id ?? null;
        
        if (!$organizationId) {
            // Fallback: get from customer metadata
            $customer = \Stripe\Customer::retrieve($subscription->customer);
            $organizationId = $customer->metadata->organization_id ?? null;
        }

        if ($organizationId) {
            $organization = Organization::find($organizationId);
            if ($organization) {
                // Check if subscription is in trial period
                $isTrial = $subscription->status === 'trialing' && isset($subscription->trial_end);
                $trialEndsAt = $isTrial ? \Carbon\Carbon::createFromTimestamp($subscription->trial_end) : null;
                
                $organization->update([
                    'is_subscribed' => true,
                    'subscription_ends_at' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_end),
                    'trial_ends_at' => $trialEndsAt,
                    'stripe_id' => $subscription->customer,
                ]);

                Log::info('Organization subscription updated', [
                    'organization_id' => $organizationId,
                    'status' => $subscription->status,
                    'is_trial' => $isTrial,
                    'trial_ends_at' => $trialEndsAt,
                ]);
            }
        }
    }

    protected function handleSubscriptionCancellation($subscription)
    {
        $organizationId = $subscription->metadata->organization_id ?? null;
        
        if (!$organizationId) {
            $customer = \Stripe\Customer::retrieve($subscription->customer);
            $organizationId = $customer->metadata->organization_id ?? null;
        }

        if ($organizationId) {
            $organization = Organization::find($organizationId);
            if ($organization) {
                $organization->update([
                    'is_subscribed' => false,
                    'subscription_ends_at' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_end),
                ]);
            }
        }
    }

    protected function handlePaymentSucceeded($invoice)
    {
        $organizationId = $invoice->metadata->organization_id ?? null;
        
        if (!$organizationId) {
            $customer = \Stripe\Customer::retrieve($invoice->customer);
            $organizationId = $customer->metadata->organization_id ?? null;
        }

        if ($organizationId) {
            $organization = Organization::find($organizationId);
            if ($organization) {
                $organization->update([
                    'is_subscribed' => true,
                ]);
            }
        }
    }

    protected function handlePaymentFailed($invoice)
    {
        $organizationId = $invoice->metadata->organization_id ?? null;
        
        if (!$organizationId) {
            $customer = \Stripe\Customer::retrieve($invoice->customer);
            $organizationId = $customer->metadata->organization_id ?? null;
        }

        if ($organizationId) {
            $organization = Organization::find($organizationId);
            if ($organization) {
                // Don't immediately mark as unsubscribed - give grace period
                Log::warning('Payment failed for organization', [
                    'organization_id' => $organizationId,
                    'invoice_id' => $invoice->id,
                    'amount_due' => $invoice->amount_due,
                ]);
                
                // You can add logic here to:
                // - Send notification email
                // - Set grace period
                // - Mark for suspension after X days
            }
        }
    }

    protected function handleTrialWillEnd($subscription)
    {
        $organizationId = $subscription->metadata->organization_id ?? null;
        
        if (!$organizationId) {
            $customer = \Stripe\Customer::retrieve($subscription->customer);
            $organizationId = $customer->metadata->organization_id ?? null;
        }

        if ($organizationId) {
            $organization = Organization::find($organizationId);
            if ($organization) {
                Log::info('Trial ending soon for organization', [
                    'organization_id' => $organizationId,
                    'trial_end' => $subscription->trial_end,
                ]);
                
                // You can add logic here to send notification email
            }
        }
    }

    protected function handlePaymentActionRequired($invoice)
    {
        $organizationId = $invoice->metadata->organization_id ?? null;
        
        if (!$organizationId) {
            $customer = \Stripe\Customer::retrieve($invoice->customer);
            $organizationId = $customer->metadata->organization_id ?? null;
        }

        if ($organizationId) {
            $organization = Organization::find($organizationId);
            if ($organization) {
                Log::info('Payment action required for organization', [
                    'organization_id' => $organizationId,
                    'invoice_id' => $invoice->id,
                ]);
                
                // You can add logic here to notify user that payment action is required
            }
        }
    }

    protected function handleInvoiceUpcoming($invoice)
    {
        $organizationId = $invoice->metadata->organization_id ?? null;
        
        if (!$organizationId) {
            $customer = \Stripe\Customer::retrieve($invoice->customer);
            $organizationId = $customer->metadata->organization_id ?? null;
        }

        if ($organizationId) {
            $organization = Organization::find($organizationId);
            if ($organization) {
                Log::info('Upcoming invoice for organization', [
                    'organization_id' => $organizationId,
                    'invoice_id' => $invoice->id,
                    'amount_due' => $invoice->amount_due,
                    'period_end' => $invoice->period_end,
                ]);
                
                // You can add logic here to send renewal reminder email
            }
        }
    }

    protected function handleCustomerUpdate($customer)
    {
        $organizationId = $customer->metadata->organization_id ?? null;

        if ($organizationId) {
            $organization = Organization::find($organizationId);
            if ($organization) {
                // Update organization email if customer email changed
                if (isset($customer->email) && $customer->email !== $organization->email) {
                    $organization->update(['email' => $customer->email]);
                }
            }
        }
    }

    protected function handleCustomerDeleted($customer)
    {
        $organizationId = $customer->metadata->organization_id ?? null;

        if ($organizationId) {
            $organization = Organization::find($organizationId);
            if ($organization) {
                // Mark organization as inactive or handle deletion
                $organization->update([
                    'is_subscribed' => false,
                    'stripe_id' => null,
                ]);
                
                Log::info('Customer deleted, organization subscription cancelled', [
                    'organization_id' => $organizationId,
                ]);
            }
        }
    }

    protected function handlePaymentMethodAttached($paymentMethod)
    {
        $customerId = $paymentMethod->customer;
        
        // Get organization from customer
        $customer = \Stripe\Customer::retrieve($customerId);
        $organizationId = $customer->metadata->organization_id ?? null;

        if ($organizationId) {
            $organization = Organization::find($organizationId);
            if ($organization) {
                // Update payment method info
                $organization->update([
                    'pm_type' => $paymentMethod->type,
                    'pm_last_four' => $paymentMethod->card->last4 ?? null,
                ]);
            }
        }
    }
}
