<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Models\Waste;
use App\Models\Household;

class ReportRepository
{
    /**
     * Aggregation pickup grouped by type & status
     */
    public function getWasteSummary(): array
    {
        // MongoDB aggregation pipeline
        return Waste::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$group' => [
                        '_id'   => [
                            'type'   => '$type',
                            'status' => '$status',
                        ],
                        'total' => ['$sum' => 1],
                    ],
                ],
                [
                    '$group' => [
                        '_id'      => '$_id.type',
                        'statuses' => [
                            '$push' => [
                                'status' => '$_id.status',
                                'total'  => '$total',
                            ],
                        ],
                        'total'    => ['$sum' => '$total'],
                    ],
                ],
                ['$sort' => ['_id' => 1]],
            ])->toArray();
        });
    }

    /**
     * Total payment by status + total revenue
     */
    public function getPaymentSummary(): array
    {
        $summary = Payment::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$group' => [
                        '_id'          => '$status',
                        'total_count'  => ['$sum' => 1],
                        'total_amount' => ['$sum' => '$amount'],
                    ],
                ],
                ['$sort' => ['_id' => 1]],
            ])->toArray();
        });

        // Calculate total revenue (only paid)
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');

        return [
            'by_status'     => $summary,
            'total_revenue' => $totalRevenue,
        ];
    }

    /**
     * History pickup and payment for 1 household
     */
    public function getHouseholdHistory(string $householdId): array
    {
        $household = Household::find($householdId);

        if (!$household) return [];

        $pickups  = Waste::where('household_id', $householdId)
            ->orderBy('created_at', 'desc')
            ->get();

        $payments = Payment::where('household_id', $householdId)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'household' => $household,
            'pickups'   => $pickups,
            'payments'  => $payments,
        ];
    }
}
