<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\BillingRepositoryInterface;
use APP\Models\User;

class StripeController extends Controller
{
    protected $billingRepository;

    public function __construct(BillingRepositoryInterface $billingRepository) {
        $this->billingRepository = $billingRepository;
    }

    public function createCustomer(Request $request) {
        // Note: Data may come from various sources, not necessarily from a form.
        $this->billingRepository->createCustomer($request->all());
        // Additional logic
    }

    public function addPaymentMethod(Request $request) {
        $request->validate([
            'cardHoldersName' => 'required',
            'payment_method' => 'required',
        ]);

        $user = User::find(auth()->id());
        $paymentMethodId = $request->input('payment_method');
        $cardholderName = $request->input('payment_method');

        $data = [
            'user' => $user,
            'paymentMethodId' => $paymentMethodId,
            'cardholderName' => $cardholderName
        ];

        // Use the billingRepository to add the payment method
        $result = $this->billingRepository->addPaymentMethod($data);

        // Check the result and return a response
        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }
}
