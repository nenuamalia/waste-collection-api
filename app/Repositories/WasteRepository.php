<?php

namespace App\Repositories;

use App\Factories\WasteFactory;
use App\Models\Waste;

class WasteRepository
{
    public function getAll(array $filters = [])
    {
        $query = Waste::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['household_id'])) {
            $query->where('household_id', $filters['household_id']);
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function findById(string $id): ?Waste
    {
        $waste = Waste::find($id);
        if (!$waste) return null;

        // Resolve to the correct subclass
        return WasteFactory::resolve($waste);
    }

    public function create(array $data): Waste
    {
        // Create the appropriate subclass instance, then save
        $waste = WasteFactory::make($data['type'], $data);
        $waste->save();
        return $waste;
    }

    public function update(string $id, array $data): ?Waste
    {
        $waste = Waste::find($id);
        if (!$waste) return null;

        $waste->update($data);

        // Re-resolve after update
        return WasteFactory::resolve($waste->fresh());
    }

    public function getPendingOrganicOlderThan(int $days): \Illuminate\Support\Collection
    {
        return Waste::where('type', 'organic')
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subDays($days))
            ->get();
    }
}
