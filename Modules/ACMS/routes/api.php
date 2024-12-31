<?php

use Illuminate\Support\Facades\Route;
use Modules\ACMS\app\Http\Controllers\CircularController;
use Modules\ACMS\app\Http\Controllers\SubjectController;

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

    Route::put('/acms/circulars/edit/{id}', [CircularController::class, 'update']);

    Route::get('/acms/circulars/edit/{id}', [CircularController::class, 'edit']);

    Route::delete('/acms/circulars/delete/{id}', [CircularController::class, 'destroy']);

    Route::post('/acms/circulars/add', [CircularController::class, 'store']);

    Route::post('/acms/circulars/subject/add', [SubjectController::class, 'storeSubjectAndAttachToCircular']);

    Route::delete('/acms/circulars/subject/delete', [SubjectController::class, 'deActiveSubjectAndDetachToCircular']);

    Route::post('/acms/circulars/verified/ounits-count', [CircularController::class, 'unitsIncludingForAddingBudgetCount']);

    Route::post('/acms/circulars/dispatch', [CircularController::class, 'dispatchCircularToVillages']);
});
