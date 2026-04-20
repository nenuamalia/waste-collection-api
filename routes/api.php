<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\WasteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;

// Auth routes (public)
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login',    [AuthController::class, 'login']);

// Public
Route::apiResource('households', HouseholdController::class);

// Protected with JWT
Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::prefix('pickups')->group(function () {
        Route::get('/',             [WasteController::class, 'index']);
        Route::post('/',            [WasteController::class, 'store']);
        Route::put('{id}/schedule', [WasteController::class, 'schedule']);
        Route::put('{id}/complete', [WasteController::class, 'complete']);
        Route::put('{id}/cancel',   [WasteController::class, 'cancel']);
    });

    Route::prefix('payments')->group(function () {
        Route::get('/',            [PaymentController::class, 'index']);
        Route::post('/',           [PaymentController::class, 'store']);
        Route::put('{id}/confirm', [PaymentController::class, 'confirm']);
    });

    Route::prefix('reports')->group(function () {
        Route::get('waste-summary',           [ReportController::class, 'wasteSummary']);
        Route::get('payment-summary',         [ReportController::class, 'paymentSummary']);
        Route::get('households/{id}/history', [ReportController::class, 'householdHistory']);
    });
});
