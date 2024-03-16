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

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::post('/products/merchandise/list/filter', [\Modules\Merchandise\app\Http\Controllers\MerchandiseController::class, 'indexFilterData']);
});
Route::middleware(['auth:api','route'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/products/merchandise/add', [\Modules\Merchandise\app\Http\Controllers\MerchandiseController::class, 'store']);
    Route::get('/products/merchandise/add', [\Modules\Merchandise\app\Http\Controllers\MerchandiseController::class, 'addBaseInfo']);
    Route::post('/products/merchandise/list', [\Modules\Merchandise\app\Http\Controllers\MerchandiseController::class, 'index']);
    Route::put('/products/merchandise/update/{id}', [\Modules\Merchandise\app\Http\Controllers\MerchandiseController::class, 'update']);
    Route::post('/products/merchandise/{id}', [\Modules\Merchandise\app\Http\Controllers\MerchandiseController::class, 'show']);
    Route::post('/products/merchandise/update/{id}', [\Modules\Merchandise\app\Http\Controllers\MerchandiseController::class, 'show']);
    Route::delete('/products/merchandise/delete/{id}', [\Modules\Merchandise\app\Http\Controllers\MerchandiseController::class, 'destroy']);
});
