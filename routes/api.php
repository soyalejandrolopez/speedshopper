<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\PackageController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PurchaseRequestController;
use App\Http\Controllers\Api\V1\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('requests', PurchaseRequestController::class)
            ->parameters(['requests' => 'purchaseRequest']);
        Route::apiResource('packages', PackageController::class);
        Route::apiResource('shipments', ShipmentController::class);
        Route::apiResource('payments', PaymentController::class);
    });
});
