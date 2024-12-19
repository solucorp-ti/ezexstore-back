<?php

use App\Http\Controllers\Api\V1\InventoryLogController;
use App\Http\Controllers\Api\V1\ProductPhotoController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\TenantController;
use App\Http\Controllers\Api\V1\TestController;
use App\Http\Controllers\Api\V1\WarehouseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {

    Route::post('tenants', [TenantController::class, 'store']);

    Route::middleware('api.key')->group(function () {

        Route::get('/test', [TestController::class, 'testConnection']);

        Route::put('products', [ProductController::class, 'update']);

        Route::get('products/{product}/photos', [ProductPhotoController::class, 'index']);
        Route::post('products/{product}/photos', [ProductPhotoController::class, 'store']);
        Route::delete('products/{product}/photos/{photo}', [ProductPhotoController::class, 'destroy']);

        Route::get('inventory-logs', [InventoryLogController::class, 'index']);

        Route::apiResource('tenants', TenantController::class)->only(['update']);

        Route::get('warehouses', [WarehouseController::class, 'index']);

        Route::prefix('inventory')->group(function () {
            Route::post('adjust', [InventoryController::class, 'adjustStock']);
            Route::get('stock', [InventoryController::class, 'getStock']);
        });
    });
});
