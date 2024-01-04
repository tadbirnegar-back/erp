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

Route::middleware(['auth:api'])->prefix('v1/branch')->name('api.')->group(function () {
    Route::post('list',[\Modules\BranchMS\app\Http\Controllers\BranchMSController::class,'index']);
    Route::post('add',[\Modules\BranchMS\app\Http\Controllers\BranchMSController::class,'store']);
    Route::post('{id}',[\Modules\BranchMS\app\Http\Controllers\BranchMSController::class,'show']);
    Route::put('{id}',[\Modules\BranchMS\app\Http\Controllers\BranchMSController::class,'update']);
    Route::prefix('department')->group(function () {
        Route::put('{id}',[\Modules\BranchMS\app\Http\Controllers\BranchMSController::class,'update']);

    });
});
