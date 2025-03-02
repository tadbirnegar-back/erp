<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\EVAL\app\Http\Controllers\CircularController;

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
Route::post('/eval/evaluation/revising/{id}' , [EvaluationController::class, 'revising']);
Route::get('/eval/make/evaluation-form/{id}' , [EvaluationController::class, 'makeEvaluationForm']);
Route::get('/eval/make/re-evaluation-form/{id}' , [EvaluationController::class, 'remakeEvaluationForm']);
Route::post('eval/add/circular',[CircularController::class,'create']);
Route::post('eval/circular/list',[CircularController::class,'circularSearch']);
Route::get('eval/single/{id}',[CircularController::class,'single']);
Route::get('eval/last/circular/{id}',[CircularController::class,'showLastCircularData']);
Route::get('eval/update/circular/{id}',[CircularController::class,'editCircular']);
Route::get('eval/delete/circular/{id}',[CircularController::class,'circularDelete']);
Route::post('eval/arzyabi/list',[CircularController::class,'evaluationList']);
Route::get('eval/items/list/{id}',[CircularController::class,'itemList']);
Route::get('eval/variable/drop-down/list/{id}',[CircularController::class,'dropDownsToAddVariable']);
Route::get('/eval/evaluation/revising/{id}', [EvaluationController::class, 'revisingEvaluationPreData']);
Route::post('eval/add/circular', [CircularController::class, 'create']);
Route::post('eval/circular/list', [CircularController::class, 'circularSearch']);
Route::get('eval/single/{id}', [CircularController::class, 'single']);
Route::get('eval/last/circular/{id}', [CircularController::class, 'showLastCircularData']);
Route::post('eval/update/circular/{id}', [CircularController::class, 'editCircular']);
Route::get('eval/delete/circular/{id}', [CircularController::class, 'circularDelete']);
Route::get('/eval/evaluating/list',[CircularController::class,'evaluationList']);
Route::get('/eval/evaluating/district',[CircularController::class,'listForDistrict']);
Route::get('eval/items/list/{id}', [CircularController::class, 'itemList']);
Route::get('eval/variable/drop-down/list/{id}', [CircularController::class, 'dropDownsToAddVariable']);
Route::post('eval/properties/list', [CircularController::class, 'listingProperties']);
Route::get('eval/add/variable/{id}', [CircularController::class, 'createVariable']);
Route::post('eval/update/variable/{id}', [CircularController::class, 'updateVariable']);
