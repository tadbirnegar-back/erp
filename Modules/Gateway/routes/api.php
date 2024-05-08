<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::middleware(['auth:api'])->prefix('v1')->name('api.')->group(function () {
    Route::post('payments/list', [\Modules\Gateway\app\Http\Controllers\GatewayController::class,'index']);
    Route::post('start', [\Modules\Gateway\app\Http\Controllers\GatewayController::class,'startPayment']);
    Route::post('verify', [\Modules\Gateway\app\Http\Controllers\GatewayController::class,'verifyPayment']);
});
