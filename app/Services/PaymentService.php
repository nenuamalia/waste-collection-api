<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use App\Repositories\HouseholdRepository;
use Exception;

class PaymentService
{
    public function __construct(
        protected PaymentRepository   $paymentRepository,
        protected HouseholdRepository $householdRepository,
    ) {}

    public function getAll(array $filters = [])
    {
        return $this->paymentRepository->getAll($filters);
    }

    public function create(array $data)
    {
        // Check if household is exist
        $household = $this->householdRepository->findById($data['household_id']);
        if (!$household) {
            throw new Exception('Household not found.', 404);
        }

        // Create payment
        return $this->paymentRepository->create($data);
    }

    public function confirm(string $id)
    {
        // Check if payment is exist
        $payment = $this->paymentRepository->findById($id);

        if (!$payment) {
            throw new Exception('Payment not found.', 404);
        }

        // Check if payment is pending
        if ($payment->status !== 'pending') {
            throw new Exception('Only pending payment can be confirmed.', 422);
        }

        // Confirm payment
        return $this->paymentRepository->confirm($id);
    }
}
