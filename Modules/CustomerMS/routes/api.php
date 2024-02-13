<?php

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

Route::middleware(['auth:api','route'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/customers/add', [\Modules\CustomerMS\app\Http\Controllers\CustomerMSController::class, 'store']);
    Route::post('/customers/natural/search', [\Modules\CustomerMS\app\Http\Controllers\CustomerMSController::class, 'naturalIsCustomer']);
    Route::post('/customers/legal/search', [\Modules\CustomerMS\app\Http\Controllers\CustomerMSController::class, 'legalIsCustomer']);
//    Route::post('/customers/legal/add', [\Modules\customersMS\app\Http\Controllers\customersMSController::class, 'legalStore']);
    Route::post('/customers/list', [\Modules\CustomerMS\app\Http\Controllers\CustomerMSController::class, 'index']);
//    Route::post('/customers/legal/list', [\Modules\customersMS\app\Http\Controllers\customersMSController::class, 'legalIndex']);
//    Route::delete('/customers/natural/delete/{id}', [\Modules\customersMS\app\Http\Controllers\customersMSController::class, 'naturalDestroy']);
    Route::put('/customers/update/{id}', [\Modules\CustomerMS\app\Http\Controllers\CustomerMSController::class, 'update']);
//    Route::put('/customers/legal/update/{id}', [\Modules\customersMS\app\Http\Controllers\customersMSController::class, 'legalUpdate']);
    Route::post('/customers/{id}', [\Modules\CustomerMS\app\Http\Controllers\CustomerMSController::class, 'show']);
//    Route::post('/customers/legal/{id}', [\Modules\customersMS\app\Http\Controllers\customersMSController::class, 'legalShow']);
    Route::post('/customers/edit/{id}', [\Modules\CustomerMS\app\Http\Controllers\CustomerMSController::class, 'show']);
//    Route::post('/customers/legal/edit/{id}', [\Modules\customersMS\app\Http\Controllers\customersMSController::class, 'legalShow']);
    Route::delete('/customers/delete/{id}', [\Modules\CustomerMS\app\Http\Controllers\CustomerMSController::class, 'destroy']);

});
