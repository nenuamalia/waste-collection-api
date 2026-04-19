<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Traits\ApiResponse;

class ReportController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ReportService $reportService
    ) {}

    public function wasteSummary()
    {
        //
    }

    public function paymentSummary()
    {
        //
    }

    public function householdHistory(string $id)
    {
        //
    }
}
