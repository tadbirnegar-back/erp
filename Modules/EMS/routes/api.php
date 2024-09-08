<?php

use Illuminate\Support\Facades\Route;
use Modules\EMS\app\Http\Controllers\EMSController;
use Modules\EMS\app\Http\Controllers\EnactmentController;

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
    Route::get('mes/enactment/add-by-board', [EMSController::class, 'addBaseInfo']);

    Route::post('mes/enactment/add-by-board', [EnactmentController::class, 'store']);

    Route::post('mes/ounit-villages/search', [EnactmentController::class, 'getMyVillagesToAddEnactment']);

    Route::post('mes/pbs-enactments/list', [EnactmentController::class, 'indexSecretary']);

    Route::post('mes/pbc-enactments/list', [EnactmentController::class, 'indexHeyaat']);

    Route::post('mes/all-enactments/list', [EnactmentController::class, 'indexArchive']);

});
