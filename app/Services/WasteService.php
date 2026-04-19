<?php

namespace App\Services;

use App\Models\Waste;
use App\Repositories\WasteRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\HouseholdRepository;
use Exception;

class WasteService
{
    public function __construct(
        protected WasteRepository     $wasteRepository,
        protected PaymentRepository   $paymentRepository,
        protected HouseholdRepository $householdRepository,
    ) {}

    public function getAll(array $filters = [])
    {
        return $this->wasteRepository->getAll($filters);
    }

    public function create(array $data): Waste
    {
        // Check unpaid payment
        $hasUnpaid = $this->paymentRepository
            ->hasUnpaidByHousehold($data['household_id']);

        if ($hasUnpaid) {
            throw new Exception(
                'Household still has unpaid bills.', 422
            );
        }

        // Check if household is exist
        $household = $this->householdRepository->findById($data['household_id']);
        if (!$household) {
            throw new Exception('Household not found.', 404);
        }

        return $this->wasteRepository->create($data);
    }

    public function schedule(string $id, string $pickupDate): Waste
    {
        // Check if waste is exist
        $waste = $this->wasteRepository->findById($id);

        if (!$waste) {
            throw new Exception('Pickup not found.', 404);
        }

        // Check if waste can be scheduled
        if (!$waste->canBeScheduled()) {
            throw new Exception(
                'Pickup cannot be scheduled. Make sure the waste is not completed or cancelled' .
                ($waste->type === 'electronic' ? ' and safety_check is checked.' : '.'),
                422
            );
        }

        // Schedule waste
        return $this->wasteRepository->update($id, [
            'status'      => 'scheduled',
            'pickup_date' => $pickupDate,
        ]);
    }

    public function complete(string $id): Waste
    {
        // Check if waste is exist
        $waste = $this->wasteRepository->findById($id);

        if (!$waste) {
            throw new Exception('Pickup not found.', 404);
        }

        // Check if waste is scheduled
        if ($waste->status !== 'scheduled') {
            throw new Exception('Only scheduled waste can be completed.', 422);
        }

        // Complete waste
        $waste = $this->wasteRepository->update($id, ['status' => 'completed']);

        // Automatically create payment after completed
        $this->paymentRepository->create([
            'household_id' => $waste->household_id,
            'waste_id'     => $waste->id,
            'amount'       => $waste->getPaymentAmount(),
            'status'       => 'pending',
        ]);

        return $waste;
    }

    public function cancel(string $id): Waste
    {
        // Check if waste is exist
        $waste = $this->wasteRepository->findById($id);

        if (!$waste) {
            throw new Exception('Pickup not found.', 404);
        }

        // Check if waste can be cancelled
        if (!in_array($waste->status, ['pending', 'scheduled'])) {
            throw new Exception('Pickup cannot be cancelled.', 422);
        }

        // Cancel waste
        return $this->wasteRepository->update($id, ['status' => 'canceled']);
    }
}
