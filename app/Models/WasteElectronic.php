<?php

namespace App\Models;

class WasteElectronic extends Waste
{
    protected $fillable = [
        'household_id',
        'type',
        'status',
        'pickup_date',
        'safety_check', // field tambahan khusus electronic
    ];

    protected $attributes = [
        'status'       => 'pending',
        'type'         => 'electronic',
        'safety_check' => false,
    ];

    // Override: electronic needs safety_check = true
    public function canBeScheduled(): bool
    {
        return $this->status === 'pending' && $this->safety_check === true;
    }

    // Override: electronic has higher payment amount
    public function getPaymentAmount(): int
    {
        return 100000;
    }
}
