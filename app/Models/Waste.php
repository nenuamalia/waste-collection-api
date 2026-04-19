<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Waste extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'wastes';

    protected $fillable = [
        'household_id',
        'type',
        'status',
        'pickup_date',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    // Relation
    public function household()
    {
        return $this->belongsTo(Household::class, 'household_id');
    }

    // Polymorphism Method
    public function getPaymentAmount(): int
    {
        return 50000;
    }

    public function canBeScheduled(): bool
    {
        return $this->status === 'pending';
    }
}
