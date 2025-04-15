<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\PFM\app\Http\Controllers\CircularController;
use Modules\PFM\app\Http\Controllers\LevyItemsController;

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
});

Route::post('/pfm/circular/store', [CircularController::class, 'store']);
Route::get('/pfm/circulars/list', [CircularController::class, 'index']);
Route::get('/pfm/circular/show/{id}', [CircularController::class, 'show']);
Route::post('/pfm/circular/update/{id}', [CircularController::class, 'update']);
Route::get('/pfm/circular/update/{id}', [CircularController::class, 'showForUpdate']);
Route::post('/pfm/circulars/publish/{id}', [CircularController::class, 'generateBooklets']);
Route::get('/pfm/circular/delete/{id}', [CircularController::class, 'delete']);
Route::post('/pfm/levy-items/store/{id}', [LevyItemsController::class, 'store']);
Route::get('/pfm/levy-items/delete/{id}', [LevyItemsController::class, 'delete']);
Route::get('/pfm/levy-items/index/{id}', [LevyItemsController::class, 'index']);
