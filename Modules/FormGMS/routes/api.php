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

Route::middleware(['auth:api','route'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/forms/add', [\Modules\FormGMS\app\Http\Controllers\FormGMSController::class, 'store']);
    Route::get('/forms/add', [\Modules\FormGMS\app\Http\Controllers\FormGMSController::class, 'getBaseInfo']);
    Route::post('/forms/list', [\Modules\FormGMS\app\Http\Controllers\FormGMSController::class, 'index']);
    Route::post('/forms/{id}', [\Modules\FormGMS\app\Http\Controllers\FormGMSController::class, 'show']);
    Route::post('/forms/update/{id}', [\Modules\FormGMS\app\Http\Controllers\FormGMSController::class, 'show'])->name('forms.update');
    Route::put('/forms/update/{id}', [\Modules\FormGMS\app\Http\Controllers\FormGMSController::class, 'update']);
    Route::delete('/forms/delete/{id}', [\Modules\FormGMS\app\Http\Controllers\FormGMSController::class, 'destroy']);
});
