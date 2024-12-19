<?php

use App\Http\Controllers\Api\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\ProductPhotoController;
use App\Http\Controllers\Api\InventoryLogController;

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

Route::middleware('api.key')->prefix('v1')->group(function () {
    Route::get('/test', [TestController::class, 'testConnection']);

    // Rutas de productos
    Route::apiResource('products', ProductController::class);

    Route::get('products/{product}/photos', [ProductPhotoController::class, 'index']);
    Route::post('products/{product}/photos', [ProductPhotoController::class, 'store']);
    Route::delete('products/{product}/photos/{photo}', [ProductPhotoController::class, 'destroy']);

    Route::get('inventory-logs', [InventoryLogController::class, 'index']);

    Route::apiResource('tenants', TenantController::class)->only(['store', 'update']);

    Route::get('warehouses', [WarehouseController::class, 'index']);

    // Rutas de inventario
    Route::prefix('inventory')->group(function () {
        Route::post('adjust', [InventoryController::class, 'adjustStock']);
        Route::get('stock', [InventoryController::class, 'getStock']);
    });
});
