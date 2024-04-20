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

Route::middleware(['auth:api'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/evaluations/partake/{id}', [\Modules\EvalMS\app\Http\Controllers\EvaluatorController::class, 'store']);
});

Route::middleware(['auth:api'])->prefix('v1')->name('api.')->group(function () {
//    Route::post('/evaluations/add', [\Modules\EvalMS\app\Http\Controllers\EvaluatorController::class, 'store']);
    Route::post('/evaluations/list', [\Modules\EvalMS\app\Http\Controllers\EvaluationController::class, 'index']);
//    Route::delete('/evaluations/delete/{id}', [\Modules\FileMS\app\Http\Controllers\FileMSController::class, 'destroy']);
//    Route::put('/evaluations/update/{id}', [\Modules\FileMS\app\Http\Controllers\FileMSController::class, 'update']);
    Route::get('/evaluations/partake/{id}', [\Modules\EvalMS\app\Http\Controllers\EvaluationController::class, 'show']);

    Route::post('/evaluations/partake/check/{id}', [\Modules\EvalMS\app\Http\Controllers\EvaluatorController::class, 'hasEvaluationRecord']);
});
