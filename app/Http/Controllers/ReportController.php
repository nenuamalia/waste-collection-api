<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Traits\ApiResponse;
use Exception;

class ReportController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ReportService $reportService
    ) {}

    public function wasteSummary()
    {
        $data = $this->reportService->getWasteSummary();
        return $this->successResponse($data, 'Waste summary retrieved successfully.');
    }

    public function paymentSummary()
    {
        $data = $this->reportService->getPaymentSummary();
        return $this->successResponse($data, 'Payment summary retrieved successfully.');
    }

    public function householdHistory(string $id)
    {
        try {
            $data = $this->reportService->getHouseholdHistory($id);
            return $this->successResponse($data, 'Household history retrieved successfully.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
