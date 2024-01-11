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
    Route::post('/file/add', [\Modules\FileMS\app\Http\Controllers\FileMSController::class, 'store']);
    Route::delete('/file/delete/{id}', [\Modules\FileMS\app\Http\Controllers\FileMSController::class, 'destroy']);
    Route::post('/file/{id}', [\Modules\FileMS\app\Http\Controllers\FileMSController::class, 'show']);
});
