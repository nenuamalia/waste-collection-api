<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHouseholdRequest;
use App\Http\Requests\UpdateHouseholdRequest;
use App\Http\Resources\HouseholdResource;
use App\Services\HouseholdService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class HouseholdController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected HouseholdService $householdService
    ) {}

    public function index(Request $request)
    {
        $households = $this->householdService->getAll($request->all());
        return $this->successResponse(
            HouseholdResource::collection($households)
        );
    }

    public function store(StoreHouseholdRequest $request)
    {
        $household = $this->householdService->create($request->validated());
        return $this->successResponse(
            new HouseholdResource($household),
            'Household successfully created.',
            201
        );
    }

    public function show(string $id)
    {
        $household = $this->householdService->findById($id);

        if (!$household) {
            return $this->errorResponse('Household not found.', 404);
        }

        return $this->successResponse(new HouseholdResource($household));
    }

    public function update(UpdateHouseholdRequest $request, string $id)
    {
        $household = $this->householdService->update($id, $request->validated());

        if (!$household) {
            return $this->errorResponse('Household not found.', 404);
        }

        return $this->successResponse(
            new HouseholdResource($household),
            'Household successfully updated.'
        );
    }

    public function destroy(string $id)
    {
        $deleted = $this->householdService->delete($id);

        if (!$deleted) {
            return $this->errorResponse('Household not found.', 404);
        }

        return $this->successResponse(null, 'Household successfully deleted.');
    }
}
