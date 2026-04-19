<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Household extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'households';

    protected $fillable = [
        'owner_name',
        'address',
        'block',
        'no',
    ];
}
