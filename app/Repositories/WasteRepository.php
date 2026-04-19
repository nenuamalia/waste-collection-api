<?php

namespace App\Repositories;

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
        return Waste::find($id);
    }

    public function create(array $data): Waste
    {
        return Waste::create($data);
    }

    public function update(string $id, array $data): ?Waste
    {
        $waste = $this->findById($id);
        if ($waste) {
            $waste->update($data);
        }
        return $waste;
    }
}
