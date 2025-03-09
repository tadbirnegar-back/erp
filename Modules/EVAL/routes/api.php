<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\EVAL\app\Http\Controllers\CircularController;

use Modules\EVAL\app\Http\Controllers\EVALController;
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
    Route::post('eval/update/circular/{id}',[CircularController::class,'editCircular']);
    Route::get('eval/delete/circular/{id}',[CircularController::class,'circularDelete']);
    Route::post('eval/evaluating/list',[CircularController::class,'evaluationList']);
    Route::get('eval/items/list/{id}',[CircularController::class,'itemList']);
    Route::get('eval/variable/drop-down/list/{id}',[CircularController::class,'dropDownsToAddVariable']);
    Route::get('eval/edit/variable/drop-down/list/{id}',[CircularController::class,'dropDownsToEditVariable']);
    Route::post('/eval/evaluating/district',[CircularController::class,'listForDistrictWaitingAndCompletedList']);
    Route::post('eval/properties/list/{id}', [CircularController::class, 'listingProperties']);
    Route::post('eval/add/variable/{id}', [CircularController::class, 'createVariable']);
    Route::post('eval/update/variable/{id}', [CircularController::class, 'updateVariable']);
    Route::post('eval/edit/section/{id}', [CircularController::class, 'sectionEdit']);
    Route::post('eval/edit/indicator/{id}', [CircularController::class, 'indicatorEdit']);
    Route::post('eval/delete/section/{id}', [CircularController::class, 'sectionDelete']);
    Route::post('eval/delete/indicator/{id}', [CircularController::class, 'indicatorDelete']);
    Route::get('eval/edit/requirement/{id}', [CircularController::class, 'editVariableRequirement']);
    Route::post('eval/delete/variable/{id}', [CircularController::class, 'variableDelete']);
    Route::get('eval/wait-to-complete/list', [CircularController::class, 'listForDistrictCompleted']);
    Route::get('eval/properties/list/edit/{id}', [CircularController::class, 'listingPropertiesForEdit']);
    Route::get('/eval/merge/old/eval-to/new' , [EvalController::class, 'mergeOldEvaluationToNew']);
    Route::post('/eval/merge/old-to-new/answers/{id}' , [EvalController::class, 'fillTheAnswers']);

});
Route::middleware(['auth:api', 'route'])->prefix('v1')->group(function () {
});

Route::get('eval/single/{id}',[CircularController::class,'single']);
