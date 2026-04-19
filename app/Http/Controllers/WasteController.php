<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePickupRequest;
use App\Http\Requests\SchedulePickupRequest;
use App\Http\Resources\WasteResource;
use App\Services\WasteService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;

class WasteController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected WasteService $wasteService
    ) {}

    public function index(Request $request)
    {
        $pickups = $this->wasteService->getAll($request->all());
        return $this->successResponse(
            WasteResource::collection($pickups)
        );
    }

    public function store(StorePickupRequest $request)
    {
        try {
            $pickup = $this->wasteService->create($request->validated());
            return $this->successResponse(
                new WasteResource($pickup),
                'Pickup request successfully created.',
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function schedule(SchedulePickupRequest $request, string $id)
    {
        try {
            $pickup = $this->wasteService->schedule($id, $request->pickup_date);
            return $this->successResponse(
                new WasteResource($pickup),
                'Pickup successfully scheduled.'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function complete(string $id)
    {
        try {
            $pickup = $this->wasteService->complete($id);
            return $this->successResponse(
                new WasteResource($pickup),
                'Pickup successfully completed and payment has been created.'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function cancel(string $id)
    {
        try {
            $pickup = $this->wasteService->cancel($id);
            return $this->successResponse(
                new WasteResource($pickup),
                'Pickup successfully cancelled.'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
