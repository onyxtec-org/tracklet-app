<?php

namespace App\Contracts;

interface BillingRepositoryInterface {
    public function createCustomer($data);
    public function addPaymentMethod($data);
}
