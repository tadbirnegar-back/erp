<?php

use Illuminate\Support\Facades\Route;
use Modules\SMM\app\Http\Controllers\CircularController;

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

    Route::post('/smm/circulars/list', [CircularController::class, 'index']);

    Route::get('/smm/circulars/{id}', [CircularController::class, 'show']);

    Route::post('/smm/circulars/add', [CircularController::class, 'store']);

    Route::put('/smm/circulars/update-base/{id}', [CircularController::class, 'updateCircularBase']);

    Route::put('/smm/circulars/update-benefits/{id}', [CircularController::class, 'updateCircularBenefits']);

    Route::post('/smm/circulars/dispatch/{id}', [CircularController::class, 'dispatchCircular']);
});
