<?php

namespace App\Http\Controllers;

use App\Services\HouseholdService;
use Illuminate\Http\Request;

class HouseholdController extends Controller
{
    public function __construct(
        protected HouseholdService $householdService
    ) {}

    public function index(Request $request)
    {
        //
    }

    public function store(Request $request)
    {
        //
    }
}
