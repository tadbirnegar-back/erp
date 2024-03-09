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

Route::post('/employee/natural/search',[\Modules\HRMS\app\Http\Controllers\EmployeeController::class,'isPersonEmployee'])->middleware(['auth:api']);


Route::middleware(['auth:api','route'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/hrm/employee/add', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'store']);
    Route::post('/products/merchandise/variants/list', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'index']);
    Route::put('/products/merchandise/variants/update/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'update']);
    Route::post('/products/merchandise/variants/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'show']);
    Route::post('/products/merchandise/variants/update/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'show']);
    Route::delete('/products/merchandise/variants/delete/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'destroy']);
});
