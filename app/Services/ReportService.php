<?php

namespace App\Services;

use App\Repositories\ReportRepository;
use App\Repositories\HouseholdRepository;
use Exception;

class ReportService
{
    public function __construct(
        protected ReportRepository    $reportRepository,
        protected HouseholdRepository $householdRepository,
    ) {}

    public function getWasteSummary(): array
    {
        return $this->reportRepository->getWasteSummary();
    }

    public function getPaymentSummary(): array
    {
        return $this->reportRepository->getPaymentSummary();
    }

    public function getHouseholdHistory(string $id): array
    {
        $household = $this->householdRepository->findById($id);

        if (!$household) {
            throw new Exception('Household not found.', 404);
        }

        return $this->reportRepository->getHouseholdHistory($id);
    }
}
