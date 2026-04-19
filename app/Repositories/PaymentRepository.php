<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository
{
    public function getAll(array $filters = [])
    {
        $query = Payment::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['household_id'])) {
            $query->where('household_id', $filters['household_id']);
        }
        if (!empty($filters['date_from'])) {
            $query->where('payment_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('payment_date', '<=', $filters['date_to']);
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function findById(string $id): ?Payment
    {
        return Payment::find($id);
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function hasUnpaidByHousehold(string $householdId): bool
    {
        return Payment::where('household_id', $householdId)
            ->where('status', 'pending')
            ->exists();
    }

    public function confirm(string $id): ?Payment
    {
        $payment = $this->findById($id);
        if ($payment) {
            $payment->update([
                'status'       => 'paid',
                'payment_date' => now(),
            ]);
        }
        return $payment;
    }
}
