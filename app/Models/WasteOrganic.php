<?php

namespace App\Models;

class WasteOrganic extends Waste
{
    protected $attributes = [
        'status' => 'pending',
        'type'   => 'organic',
    ];

    // Override: check if organic waste pickup should be auto-cancelled
    public function shouldAutoCancelOrganicPickup(): bool
    {
        if (!$this->created_at) {
            return false;
        }

        return now()->diffInDays($this->created_at) > 3
            && $this->status === 'pending';
    }
}
