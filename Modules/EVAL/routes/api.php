<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\EVAL\app\Http\Controllers\CircularController;

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

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::get('eval', fn (Request $request) => $request->user())->name('eval');
});
Route::post('eval/add/circular',[CircularController::class,'create']);
Route::post('eval/circular/list',[CircularController::class,'circularSearch']);
Route::get('eval/single/{id}',[CircularController::class,'single']);
Route::get('eval/last/circular/{id}',[CircularController::class,'showLastCircularData']);
Route::get('eval/update/circular/{id}',[CircularController::class,'editCircular']);
Route::get('eval/delete/circular/{id}',[CircularController::class,'circularDelete']);
Route::post('eval/arzyabi/list',[CircularController::class,'evaluationList']);
Route::get('eval/items/list/{id}',[CircularController::class,'itemList']);
Route::get('eval/variable/drop-down/list/{id}',[CircularController::class,'dropDownsToAddVariable']);
