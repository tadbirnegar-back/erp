<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\PFM\app\Http\Controllers\PfmCircularController;

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


Route::middleware(['auth:api', 'route'])->prefix('v1')->group(function () {
    Route::post('/pfm/circular/store', [PfmCircularController::class, 'store']);
});

Route::post('/pfm/circular/store', [PfmCircularController::class, 'store']);
Route::get('/pfm/circulars/list', [PfmCircularController::class, 'index']);
Route::get('/pfm/circulars/show/{id}', [PfmCircularController::class, 'show']);
Route::post('/pfm/circulars/update/{id}', [PfmCircularController::class, 'update']);
Route::get('/pfm/circulars/update/{id}', [PfmCircularController::class, 'showForUpdate']);
Route::post('/pfm/circulars/publish/{id}', [PfmCircularController::class, 'generateBooklets']);
