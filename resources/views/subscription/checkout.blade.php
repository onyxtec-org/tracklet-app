@extends('layouts.contentLayoutMaster')

@section('title', 'Subscribe to Tracklet')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('content')
@include('panels.response')

<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8 col-xl-7">
        <!-- Welcome Header -->
        <div class="text-center mb-4">
            <div class="avatar avatar-xl bg-primary shadow mb-2 mx-auto">
                <div class="avatar-content">
                    <i data-feather="zap" class="font-large-1 text-white"></i>
                </div>
            </div>
            <h2 class="mb-1">Complete Your Subscription</h2>
            <p class="text-muted mb-0">Get started with <strong>{{ $organization->name }}</strong> and unlock all Tracklet features</p>
        </div>

        <!-- Subscription Plan Card -->
        <div class="card border-0 shadow-lg">
            <!-- Card Header with Badge -->
            <div class="card-header bg-gradient-primary text-white text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #7367F0 0%, #9E95F5 100%);">
                <div class="position-absolute" style="top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div class="position-absolute" style="bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div class="position-relative">
                    <span class="badge badge-light-success badge-lg mb-2 px-3 py-1">
                        <i data-feather="gift" class="font-small-3 mr-50"></i>
                        <strong>1 Month Free Trial</strong>
                    </span>
                    <h4 class="card-title mb-1 text-white font-weight-bolder">Annual Subscription Plan</h4>
                    <p class="text-white-50 mb-0 small">Full access to all features</p>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Pricing Display -->
                <div class="text-center mb-4 pb-3 border-bottom">
                    @if(isset($priceInfo) && $priceInfo)
                        <div class="d-flex align-items-baseline justify-content-center">
                            <span class="h1 font-weight-bolder text-primary mb-0">{{ $priceInfo['currency_symbol'] }}{{ number_format($priceInfo['monthly_price'], 0) }}</span>
                            <span class="h4 text-muted ml-1 mb-0">/month</span>
                        </div>
                        <p class="text-muted small mb-0 mt-1">
                            <span class="text-success font-weight-bold">{{ $priceInfo['formatted_annual'] }}/year</span> billed annually
                        </p>
                        <p class="text-muted small mb-0 mt-1">
                            <span class="badge badge-light-success">Free for 30 days</span> then {{ $priceInfo['formatted_annual'] }}/year
                        </p>
                    @else
                        <div class="d-flex align-items-baseline justify-content-center">
                            <span class="h1 font-weight-bolder text-primary mb-0">$0</span>
                            <span class="h4 text-muted ml-1 mb-0">/month</span>
                        </div>
                        <p class="text-muted small mb-0 mt-1">For the first 30 days, then billed annually</p>
                    @endif
                </div>

                <!-- Plan Features -->
                <div class="mb-4">
                    <h6 class="font-weight-bolder mb-3 d-flex align-items-center">
                        <i data-feather="check-circle" class="text-success mr-1 font-medium-2"></i>
                        What's Included
                    </h6>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i data-feather="check" class="text-success mr-2 font-small-4"></i>
                                <span class="font-weight-medium">Expense Tracking</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i data-feather="check" class="text-success mr-2 font-small-4"></i>
                                <span class="font-weight-medium">Inventory Management</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i data-feather="check" class="text-success mr-2 font-small-4"></i>
                                <span class="font-weight-medium">Asset Management</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i data-feather="check" class="text-success mr-2 font-small-4"></i>
                                <span class="font-weight-medium">Repair & Maintenance</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i data-feather="check" class="text-success mr-2 font-small-4"></i>
                                <span class="font-weight-medium">User Management</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i data-feather="check" class="text-success mr-2 font-small-4"></i>
                                <span class="font-weight-medium">Advanced Reporting</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i data-feather="check" class="text-success mr-2 font-small-4"></i>
                                <span class="font-weight-medium">Role-Based Access</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i data-feather="check" class="text-success mr-2 font-small-4"></i>
                                <span class="font-weight-medium">Priority Support</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan Details -->
                <div class="card border border-primary bg-light-primary mb-4">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <i data-feather="calendar" class="text-primary mr-2 font-small-3"></i>
                                <span class="font-weight-medium">Trial Period</span>
                            </div>
                            <span class="badge badge-light-primary">30 Days</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <i data-feather="credit-card" class="text-primary mr-2 font-small-3"></i>
                                <span class="font-weight-medium">Trial Cost</span>
                            </div>
                            <span class="badge badge-light-success">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i data-feather="refresh-cw" class="text-primary mr-2 font-small-3"></i>
                                <span class="font-weight-medium">Auto-Renewal</span>
                            </div>
                            <span class="badge badge-light-info">After Trial</span>
                        </div>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="alert alert-info mb-4 shadow-sm" role="alert" style="border-left: 4px solid #00cfe8; background-color: #e7f7f8;">
                    <div class="alert-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="avatar avatar-sm bg-info mr-2 flex-shrink-0">
                                <div class="avatar-content">
                                    <i data-feather="info" class="font-small-2 text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-1 font-weight-bolder text-info">How It Works</h6>
                                <p class="mb-0">Start with a <strong>1-month free trial</strong> with full access to all Tracklet features. No charges during the trial period. After 30 days, your annual subscription will begin automatically. You can cancel anytime during the trial without any charges.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Note -->
                <div class="alert alert-light-success mb-4 shadow-sm" role="alert" style="border-left: 4px solid #28c76f; background-color: #e8f8f0;">
                    <div class="alert-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="avatar avatar-sm bg-success mr-2 flex-shrink-0">
                                <div class="avatar-content">
                                    <i data-feather="shield" class="font-small-2 text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-1 text-success font-weight-bolder">Secure Payment</h6>
                                <p class="mb-0">Your payment information is securely processed by Stripe. We never store your credit card details on our servers.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscribe Button -->
                <form id="checkout-form" class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg font-weight-bolder py-2" id="checkout-button" style="font-size: 1.1rem;">
                        <i data-feather="credit-card" class="mr-1"></i>
                        <span>Start Free Trial & Subscribe</span>
                    </button>
                    <p class="text-center text-muted small mt-3 mb-0">
                        <i data-feather="lock" class="font-small-2 mr-50"></i>
                        By subscribing, you agree to our <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a>
                    </p>
                </form>
            </div>
        </div>

        <!-- Help Text -->
        <div class="text-center mt-4">
            <p class="text-muted small mb-0">
                <i data-feather="help-circle" class="font-small-2 mr-50"></i>
                Need help? <a href="mailto:support@tracklet.com" class="text-primary font-weight-medium">Contact Support</a>
            </p>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Initialize Feather Icons
    if (feather) {
        feather.replace({ width: 14, height: 14 });
    }

    $('#checkout-form').on('submit', function(e) {
        e.preventDefault();
        
        const button = $('#checkout-button');
        const originalText = button.html();
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Processing...');
        
        $.ajax({
            url: '{{ route("subscription.checkout.create") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success && response.data.checkout_url) {
                    window.location.href = response.data.checkout_url;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to create checkout session',
                        confirmButtonText: 'Try Again',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    button.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                button.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endsection
