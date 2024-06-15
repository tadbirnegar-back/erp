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
    Route::post('/hrm/verified', [\Modules\OUnitMS\app\Http\Controllers\VerifyInfoConformationController::class, 'hasVerified']);

    Route::get('/hrm/confirm', [\Modules\OUnitMS\app\Http\Controllers\VerifyInfoConformationController::class, 'show']);

    Route::post('/hrm/verify', [\Modules\OUnitMS\app\Http\Controllers\VerifyInfoConformationController::class, 'verify']);
    Route::put('/hrm/employee/confirm/edit', [\Modules\OUnitMS\app\Http\Controllers\VerifyInfoConformationController::class, 'update'])->middleware(['auth:api']);
});


Route::middleware(['auth:api'])->prefix('v1')->name('api.')->group(function () {

    Route::post ('/oms/cityofc/list', [\Modules\OUnitMS\app\Http\Controllers\OUnitMSController::class, 'citiesIndex']);

    Route::post('/oms/cityofc/add', [\Modules\OUnitMS\app\Http\Controllers\OUnitMSController::class, 'cityStore']);

    Route::post('/oms/districtofc/list', [\Modules\OUnitMS\app\Http\Controllers\OUnitMSController::class, 'districtsIndex']);

    Route::post('/oms/districtofc/add', [\Modules\OUnitMS\app\Http\Controllers\OUnitMSController::class, 'districtStore']);

    Route::post('/oms/townofc/list', [\Modules\OUnitMS\app\Http\Controllers\OUnitMSController::class, 'townIndex']);

    Route::post('/oms/townofc/add', [\Modules\OUnitMS\app\Http\Controllers\OUnitMSController::class, 'townStore']);

    Route::post('/oms/villageofc/list', [\Modules\OUnitMS\app\Http\Controllers\OUnitMSController::class, 'villageIndex']);

    Route::post('/oms/villageofc/add', [\Modules\OUnitMS\app\Http\Controllers\OUnitMSController::class, 'villageStore']);

    Route::post('/oms/organization_unit/{id}', [\Modules\OUnitMS\app\Http\Controllers\OUnitMSController::class, 'show']);

    Route::post('/oms/employee/search', [\Modules\OUnitMS\app\Http\Controllers\OUnitMSController::class, 'searchEmployees']);
});
