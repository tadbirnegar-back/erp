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

Route::middleware(['auth:api', 'route'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/address/add', [\Modules\AddressMS\app\Http\Controllers\AddressMSController::class, 'store']);
    Route::post('/address/list', [\Modules\AddressMS\app\Http\Controllers\AddressMSController::class, 'index']);
    Route::delete('/address/delete/{id}', [\Modules\AddressMS\app\Http\Controllers\AddressMSController::class, 'destroy']);
    Route::put('/address/update/{id}', [\Modules\AddressMS\app\Http\Controllers\AddressMSController::class, 'update']);
    Route::post('/address/{id}', [\Modules\AddressMS\app\Http\Controllers\AddressMSController::class, 'show']);
    Route::post('/address/edit/{id}', [\Modules\AddressMS\app\Http\Controllers\AddressMSController::class, 'show']);
});
Route::middleware([])->prefix('v1')->name('api.')->group(function () {
    Route::post('/countries', [\Modules\AddressMS\app\Http\Controllers\AddressMSController::class, 'countries']);
    Route::post('/states', [\Modules\AddressMS\app\Http\Controllers\AddressMSController::class, 'statesOfCountry']);
    Route::post('/cities', [\Modules\AddressMS\app\Http\Controllers\AddressMSController::class, 'citiesOfState']);
    Route::post('/districts', [\Modules\AddressMS\app\Http\Controllers\AddressMSController::class, 'districtsOfCity']);
    Route::post('/towns', [\Modules\AddressMS\app\Http\Controllers\AddressMSController::class, 'townsOfDistrict']);
    Route::post('/villages', [\Modules\AddressMS\app\Http\Controllers\AddressMSController::class, 'villagesOfTown']);
});

