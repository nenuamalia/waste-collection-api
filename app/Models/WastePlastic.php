<?php

namespace App\Models;

class WastePlastic extends Waste
{
    protected $attributes = [
        'status' => 'pending',
        'type'   => 'plastic',
    ];
}
