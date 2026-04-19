<?php

namespace App\Models;

class WastePaper extends Waste
{
    protected $attributes = [
        'status' => 'pending',
        'type'   => 'paper',
    ];
}
