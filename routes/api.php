<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\WasteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;

// Household
Route::apiResource('households', HouseholdController::class);

// Waste Pickups
Route::prefix('pickups')->group(function () {
    Route::get('/',              [WasteController::class, 'index']);
    Route::post('/',             [WasteController::class, 'store']);
    Route::put('{id}/schedule',  [WasteController::class, 'schedule']);
    Route::put('{id}/complete',  [WasteController::class, 'complete']);
    Route::put('{id}/cancel',    [WasteController::class, 'cancel']);
});

// Payments
Route::prefix('payments')->group(function () {
    Route::get('/',              [PaymentController::class, 'index']);
    Route::post('/',             [PaymentController::class, 'store']);
    Route::put('{id}/confirm',   [PaymentController::class, 'confirm']);
});

// Reports
Route::prefix('reports')->group(function () {
    Route::get('waste-summary',              [ReportController::class, 'wasteSummary']);
    Route::get('payment-summary',            [ReportController::class, 'paymentSummary']);
    Route::get('households/{id}/history',    [ReportController::class, 'householdHistory']);
});
