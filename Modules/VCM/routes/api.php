<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\VCM\app\Http\Controllers\VersionManagementController;
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
    Route::post('/vcm/version/store' , [VersionManagementController::class , 'storeVersion']);
    Route::post('/vcm/version/list' , [VersionManagementController::class , 'indexVersion']);
    Route::get('/vcm/modules/list' , [VersionManagementController::class , 'indexModules']);
});

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::get('/vcm/version/show' , [VersionManagementController::class , 'showVersion']);
});

