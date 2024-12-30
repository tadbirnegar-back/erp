<?php

use Illuminate\Support\Facades\Route;
use Modules\ACMS\app\Http\Controllers\CircularController;

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

    Route::get('/acms/circulars/list', [CircularController::class, 'index']);

    Route::get('/acms/circulars/{id}', [CircularController::class, 'show']);

    Route::post('/acms/circulars/add', [CircularController::class, 'store']);
});
