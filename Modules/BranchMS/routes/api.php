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
Route::post('v1/branch/list/active',[\Modules\BranchMS\app\Http\Controllers\BranchMSController::class,'indexActive'])->middleware('auth:api');
Route::post('v1/branch/departments/list/active',[\Modules\BranchMS\app\Http\Controllers\DepartmentController::class,'indexActive'])->middleware('auth:api');


Route::middleware(['auth:api','route'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/branch/list',[\Modules\BranchMS\app\Http\Controllers\BranchMSController::class,'index']);
    Route::post('/branch/add',[\Modules\BranchMS\app\Http\Controllers\BranchMSController::class,'store']);
    Route::post('/branch/{id}',[\Modules\BranchMS\app\Http\Controllers\BranchMSController::class,'show']);
    Route::post('/branch/edit/{id}',[\Modules\BranchMS\app\Http\Controllers\BranchMSController::class,'show']);
    Route::put('/branch/update/{id}',[\Modules\BranchMS\app\Http\Controllers\BranchMSController::class,'update']);
    Route::delete('/branch/delete/{id}',[\Modules\BranchMS\app\Http\Controllers\BranchMSController::class,'destroy']);

    Route::prefix('')->group(function () {
        Route::post('/branch/departments/list',[\Modules\BranchMS\app\Http\Controllers\DepartmentController::class,'index']);
        Route::post('/branch/departments/add',[\Modules\BranchMS\app\Http\Controllers\DepartmentController::class,'store']);
        Route::post('/branch/departments/{id}',[\Modules\BranchMS\app\Http\Controllers\DepartmentController::class,'show']);
        Route::post('/branch/departments/edit/{id}',[\Modules\BranchMS\app\Http\Controllers\DepartmentController::class,'show']);
        Route::put('/branch/departments/update/{id}',[\Modules\BranchMS\app\Http\Controllers\DepartmentController::class,'update']);
        Route::delete('/branch/departments/delete/{id}',[\Modules\BranchMS\app\Http\Controllers\DepartmentController::class,'destroy']);
    });

    Route::prefix('')->group(function () {
        Route::post('/branch/departments/sections/list',[\Modules\BranchMS\app\Http\Controllers\SectionController::class,'index']);
        Route::post('/branch/departments/sections/add',[\Modules\BranchMS\app\Http\Controllers\SectionController::class,'store']);
        Route::post('/branch/departments/sections/{id}',[\Modules\BranchMS\app\Http\Controllers\SectionController::class,'show']);
        Route::post('/branch/departments/sections/edit/{id}',[\Modules\BranchMS\app\Http\Controllers\SectionController::class,'show']);
        Route::put('/branch/departments/sections/update/{id}',[\Modules\BranchMS\app\Http\Controllers\SectionController::class,'update']);
        Route::delete('/branch/departments/sections/delete/{id}',[\Modules\BranchMS\app\Http\Controllers\SectionController::class,'destroy']);
    });
});
