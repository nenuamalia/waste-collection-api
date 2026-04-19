<?php

namespace App\Services;

use App\Repositories\HouseholdRepository;

class HouseholdService
{
    public function __construct(
        protected HouseholdRepository $householdRepository
    ) {}

    public function getAll(array $filters = [])
    {
        return $this->householdRepository->getAll($filters);
    }

    public function findById(string $id)
    {
        return $this->householdRepository->findById($id);
    }

    public function create(array $data)
    {
        return $this->householdRepository->create($data);
    }

    public function update(string $id, array $data)
    {
        return $this->householdRepository->update($id, $data);
    }

    public function delete(string $id)
    {
        return $this->householdRepository->delete($id);
    }
}
