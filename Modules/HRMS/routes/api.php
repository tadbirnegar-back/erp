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
    Route::post('/hrm/employee/add', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'store']);
    Route::get('/hrm/employee/add', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'addEmployeeBaseInfo']);
//    Route::post('/products/merchandise/variants/list', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'index']);
//    Route::put('/products/merchandise/variants/update/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'update']);
//    Route::post('/products/merchandise/variants/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'show']);
//    Route::post('/products/merchandise/variants/update/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'show']);
//    Route::delete('/products/merchandise/variants/delete/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'destroy']);
});

Route::middleware(['auth:api'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/employee/natural/search', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'isPersonEmployee']);

});

Route::middleware(['auth:api','route'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/hrm/levels/add', [\Modules\HRMS\app\Http\Controllers\LevelController::class, 'store']);
    Route::post('/hrm/levels/list', [\Modules\HRMS\app\Http\Controllers\LevelController::class, 'index']);
    Route::post('/hrm/levels/{id}', [\Modules\HRMS\app\Http\Controllers\LevelController::class, 'show']);
    Route::post('/hrm/levels/update/{id}', [\Modules\HRMS\app\Http\Controllers\LevelController::class, 'show']);
    Route::put('/hrm/levels/update/{id}', [\Modules\HRMS\app\Http\Controllers\LevelController::class, 'update']);
    Route::delete('/hrm/levels/delete/{id}', [\Modules\HRMS\app\Http\Controllers\LevelController::class, 'destroy']);


    Route::post('/hrm/positions/add', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'store']);
    Route::post('/hrm/positions/list', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'index']);
    Route::post('/hrm/positions/{id}', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'show']);
    Route::post('/hrm/positions/update/{id}', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'show']);
    Route::put('/hrm/positions/update/{id}', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'update']);
    Route::delete('/hrm/positions/delete/{id}', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'destroy']);



    Route::post('/hrm/skills/add', [\Modules\HRMS\app\Http\Controllers\SkillController::class, 'store']);
    Route::post('/hrm/skills/list', [\Modules\HRMS\app\Http\Controllers\SkillController::class, 'index']);
    Route::post('/hrm/skills/{id}', [\Modules\HRMS\app\Http\Controllers\SkillController::class, 'show']);
    Route::post('/hrm/skills/update/{id}', [\Modules\HRMS\app\Http\Controllers\SkillController::class, 'show']);
    Route::put('/hrm/skills/update/{id}', [\Modules\HRMS\app\Http\Controllers\SkillController::class, 'update']);
    Route::delete('/hrm/skills/delete/{id}', [\Modules\HRMS\app\Http\Controllers\SkillController::class, 'destroy']);
});
