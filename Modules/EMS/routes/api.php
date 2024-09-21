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

    Route::post('mes/enactments/{id}', [EnactmentController::class, 'show']);

    Route::post('mes/enactments/approve/{id}', [EnactmentController::class, 'enactmentApproval']);

    Route::post('mes/enactments/decline/{id}', [EnactmentController::class, 'enactmentDenial']);

    Route::post('mes/enactments/deny/{id}', [EnactmentController::class, 'enactmentInconsistency']);

    Route::post('mes/enactments/accept/{id}', [EnactmentController::class, 'enactmentNoInconsistency']);

    Route::post('mes/setting/secretary', [EMSController::class, 'getHeyaatMembers']);

    Route::put('mes/setting/secretary', [EMSController::class, 'updateHeyaatMembers']);

});
