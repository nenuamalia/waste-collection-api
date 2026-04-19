<?php

namespace App\Services;

use App\Repositories\WasteRepository;
use App\Repositories\PaymentRepository;

class WasteService
{
    public function __construct(
        protected WasteRepository   $wasteRepository,
        protected PaymentRepository $paymentRepository
    ) {}
}
