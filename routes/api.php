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
Route::get('health-check', function() {
    return response()->json(['status' => 'ok']);
});

Route::prefix('v1')->middleware(['api', 'json'])->group(function () {
    // Rutas públicas
    Route::post('tenants', [TenantController::class, 'store']);

    // Rutas protegidas
    Route::middleware('api.key')->group(function () {
        // Test
        Route::get('/test', [TestController::class, 'testConnection']);

        // Productos y fotos
        Route::prefix('products')->group(function () {
            Route::put('/', [ProductController::class, 'update']);
            Route::get('{product}/photos', [ProductPhotoController::class, 'index']);
            Route::post('{product}/photos', [ProductPhotoController::class, 'store']);
            Route::delete('{product}/photos/{photo}', [ProductPhotoController::class, 'destroy']);
        });

        // Inventario
        Route::prefix('inventory')->group(function () {
            Route::get('logs', [InventoryLogController::class, 'index']);
            Route::post('adjust', [InventoryController::class, 'adjustStock']);
            Route::get('stock', [InventoryController::class, 'getStock']);
        });

        // Tenants y Warehouses
        Route::put('tenants', [TenantController::class, 'update']);
        Route::get('warehouses', [WarehouseController::class, 'index']);
    });
});
