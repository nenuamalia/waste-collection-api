<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function index(Request $request)
    {
        $payments = $this->paymentService->getAll($request->all());
        return $this->successResponse(
            PaymentResource::collection($payments)
        );
    }

    public function store(StorePaymentRequest $request)
    {
        try {
            $payment = $this->paymentService->create($request->validated());
            return $this->successResponse(
                new PaymentResource($payment),
                'Payment successfully created.',
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function confirm(string $id)
    {
        try {
            $payment = $this->paymentService->confirm($id);
            return $this->successResponse(
                new PaymentResource($payment),
                'Payment successfully confirmed.'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
