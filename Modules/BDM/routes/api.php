<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\BDM\app\Http\Controllers\BDMController;
use Modules\BDM\app\Http\Controllers\EngineerController;
use Modules\BDM\app\Http\Controllers\LicenseController;
use Modules\BDM\app\Http\Controllers\EstateController;

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


Route::middleware(['auth:api', 'route'])->prefix('v1')->group(function () {
    Route::post('/bdm/licenses/list' , [LicenseController::class, 'licenseList']);
    Route::post('/bdm/related-villages/list' , [LicenseController::class, 'relatedVillagesList']);
    Route::get('/bdm/list-filter/pre_data' , [LicenseController::class, 'onlyLicenseTypesList']);
});


Route::get('/bdm/dossier/{id}' , [LicenseController::class, 'showDossier']);
Route::post('/bdm/estate/update/{id}' , [BDMController::class, 'updateEstate']);
Route::get('/bdm/license/submit/{id}' , [LicenseController::class, 'submitLicense']);
Route::post('/bdm/upload/files/{id}' , [LicenseController::class, 'uploadFiles']);
Route::get('/bdm/estates/pre-data/{id}' , [EstateController::class, 'getEstatesPreData']);
Route::post('/bdm/estate/full-fill/{id}' , [EstateController::class, 'FullFillEstate']);


Route::prefix('v1')->group(function () {
    Route::get('/bdm/license-types/list' , [LicenseController::class, 'licenseTypesList']);
    Route::post('/bdm/licenses/create' , [LicenseController::class, 'create']);
    Route::post('/bdm/engineer/request' , [EngineerController::class, 'requestEngineer']);
    Route::get('/bdm/engineers-request/pre-data' , [EngineerController::class, 'engineersRequestPreData']);
});
