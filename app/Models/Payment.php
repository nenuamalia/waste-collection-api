<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Payment extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'payments';

    protected $fillable = [
        'household_id',
        'waste_id',
        'amount',
        'payment_date',
        'status',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    public function household()
    {
        return $this->belongsTo(Household::class, 'household_id');
    }
}
