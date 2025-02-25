<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\EVAL\app\Http\Controllers\EvaluationController;
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
});
Route::middleware(['auth:api', 'route'])->prefix('v1')->group(function () {
});
Route::get('/eval/evaluation/pre-view/{id}', [EvaluationController::class, 'preViewEvaluation']);
Route::get('/eval/evaluation/start/{id}', [EvaluationController::class, 'evaluationStart']);
Route::post('/eval/evaluation/done/{id}', [EvaluationController::class, 'evaluationDone']);
Route::get('/eval/evaluation/revising/{id}' , [EvaluationController::class, 'revisingEvaluationPreData']);
