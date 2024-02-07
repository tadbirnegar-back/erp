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

Route::middleware(['auth:api', 'route'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/person/natural/add', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalStore']);
    Route::post('/person/natural/search', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalExists']);
    Route::post('/person/legal/add', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'legalStore']);
    Route::post('/person/natural/list', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalIndex']);
    Route::post('/person/legal/list', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'legalIndex']);
    Route::delete('/person/natural/delete/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalDestroy']);
    Route::delete('/person/legal/delete/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'legalDestroy']);
    Route::put('/person/natural/update/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalUpdate']);
    Route::put('/person/legal/update/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'legalUpdate']);
    Route::post('/person/natural/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalShow']);
    Route::post('/person/legal/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'legalShow']);
    Route::post('/person/natural/edit/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalShow']);
    Route::post('/person/legal/edit/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'legalShow']);
});
