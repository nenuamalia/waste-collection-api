<?php

namespace App\Repositories;

use App\Models\Household;

class HouseholdRepository
{
    public function getAll(array $filters = [])
    {
        $query = Household::query();

        if (!empty($filters['block'])) {
            $query->where('block', $filters['block']);
        }
        if (!empty($filters['no'])) {
            $query->where('no', $filters['no']);
        }
        if (!empty($filters['search'])) {
            $query->where('owner_name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function findById(string $id): ?Household
    {
        return Household::find($id);
    }

    public function create(array $data): Household
    {
        return Household::create($data);
    }

    public function update(string $id, array $data): ?Household
    {
        $household = $this->findById($id);
        if ($household) {
            $household->update($data);
        }
        return $household;
    }

    public function delete(string $id): bool
    {
        $household = $this->findById($id);
        if ($household) {
            return $household->delete();
        }
        return false;
    }
}
