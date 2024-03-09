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

Route::middleware(['auth:api','route'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/products/merchandise/variants/add', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'store']);
    Route::post('/products/merchandise/variants/list', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'index']);
    Route::put('/products/merchandise/variants/update/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'update']);
    Route::post('/products/merchandise/variants/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'show']);
    Route::post('/products/merchandise/variants/update/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'show']);
    Route::delete('/products/merchandise/variants/delete/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'destroy']);
});
Route::middleware(['auth:api',])->prefix('v1')->name('api.')->group(function () {
    Route::post('/products/merchandise/category/add', [\Modules\ProductMS\app\Http\Controllers\CategoryController::class, 'store']);
    Route::post('/products/merchandise/category/list', [\Modules\ProductMS\app\Http\Controllers\CategoryController::class, 'index']);
    Route::put('/products/merchandise/category/update/{id}', [\Modules\ProductMS\app\Http\Controllers\CategoryController::class, 'update']);
    Route::post('/products/merchandise/category/{id}', [\Modules\ProductMS\app\Http\Controllers\CategoryController::class, 'show']);
    Route::post('/products/merchandise/category/update/{id}', [\Modules\ProductMS\app\Http\Controllers\CategoryController::class, 'show']);
    Route::delete('/products/merchandise/category/delete/{id}', [\Modules\ProductMS\app\Http\Controllers\CategoryController::class, 'destroy']);
});
