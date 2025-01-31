<?php

use Illuminate\Support\Facades\Route;
use Modules\OUnitMS\app\Http\Controllers\DepartmentController;
use Modules\OUnitMS\app\Http\Controllers\OUnitMSController;

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
    Route::post('/hrm/verified', [\Modules\OUnitMS\app\Http\Controllers\VerifyInfoConformationController::class, 'hasVerified']);

    Route::get('/hrm/confirm', [\Modules\OUnitMS\app\Http\Controllers\VerifyInfoConformationController::class, 'show']);

    Route::post('/hrm/verify', [\Modules\OUnitMS\app\Http\Controllers\VerifyInfoConformationController::class, 'verify']);
    Route::put('/hrm/employee/confirm/edit', [\Modules\OUnitMS\app\Http\Controllers\VerifyInfoConformationController::class, 'update'])->middleware(['auth:api']);


});


Route::middleware(['auth:api', 'route'])->prefix('v1')->name('api.')->group(function () {

    Route::post('/oms/cityofc/list', [OUnitMSController::class, 'citiesIndex']);

    Route::post('/oms/cityofc/add', [OUnitMSController::class, 'cityStore']);

    Route::post('/oms/districtofc/list', [OUnitMSController::class, 'districtsIndex']);

    Route::post('/oms/districtofc/add', [OUnitMSController::class, 'districtStore']);

    Route::post('/oms/townofc/list', [OUnitMSController::class, 'townIndex']);

    Route::post('/oms/townofc/add', [OUnitMSController::class, 'townStore']);

    Route::post('/oms/villageofc/list', [OUnitMSController::class, 'villageIndex']);

    Route::post('/oms/villageofc/add', [OUnitMSController::class, 'villageStore']);

    Route::post('/oms/organization_unit/{id}', [OUnitMSController::class, 'show']);

    Route::post('/oms/organization_unit/update/{id}', [OUnitMSController::class, 'show']);

    Route::put('/oms/organization_unit/update/{id}', [OUnitMSController::class, 'update']);

    Route::post('/oms/department/add', [DepartmentController::class, 'store']);

    Route::post('/oms/department/list', [DepartmentController::class, 'index']);

    Route::get('/oms/department/{id}', [DepartmentController::class, 'show']);

    Route::put('/oms/department/{id}', [DepartmentController::class, 'update']);

    Route::delete('/oms/ounit/delete/{id}', [OUnitMSController::class, 'destroy']);

});

Route::middleware([])->prefix('v1')->name('api.')->group(function () {

    Route::post('/oms/employee/search', [OUnitMSController::class, 'searchEmployees']);

    Route::post('/oms/organization-unit/search', [OUnitMSController::class, 'search']);

    Route::post('/oms/village/search', [OUnitMSController::class, 'villageSearchByName']);

    Route::post('/oms/districtofc/all/list', [OUnitMSController::class, 'districtsAllList']);

});
