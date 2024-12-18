<?php

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

Route::middleware('api.key')->group(function () {
    Route::get('/test', function () {
        return response()->json([
            'message' => 'API key is valid',
            'tenant' => request('tenant')->name,
            'user' => request('api_user')->name
        ]);
    });
});
