<?php

namespace App\Services;

use App\Repositories\PaymentRepository;

class PaymentService
{
    public function __construct(
        protected PaymentRepository $paymentRepository
    ) {}
}
