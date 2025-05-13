<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\BDM\app\Http\Controllers\BDMController;
use Modules\BDM\app\Http\Controllers\EngineerController;
use Modules\BDM\app\Http\Controllers\LicenseController;
use Modules\BDM\app\Http\Controllers\EstateController;
use Modules\BDM\app\Http\Controllers\StructuresController;

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
    Route::get('/bdm/dossier/{id}' , [LicenseController::class, 'showDossier']);
    Route::get('/bdm/make-archive/{id}' , [LicenseController::class, 'makeArchive']);
    Route::post('/bdm/estate/update/{id}' , [BDMController::class, 'updateEstate']);
    Route::get('/bdm/estate/update/{id}' , [BDMController::class, 'updateEstatePreData']);
    Route::get('/bdm/license/submit/{id}' , [LicenseController::class, 'submitLicense']);
    Route::post('/bdm/upload/files/{id}' , [LicenseController::class, 'uploadFiles']);
    Route::get('/bdm/estates/pre-data/{id}' , [EstateController::class, 'getEstatesPreData']);
    Route::post('/bdm/estate/full-fill/{id}' , [EstateController::class, 'FullFillEstate']);
    Route::post('/bdm/decline-dossier/{id}' , [LicenseController::class, 'declineDossier']);
    Route::post('/bdm/engineer/detector' , [EngineerController::class, 'detectEngineer']);
    Route::post('/bdm/add-engineers/{id}' , [EngineerController::class, 'addEngineers']);
    Route::get('/bdm/engineers-type/list' , [EngineerController::class, 'engineersTypeList']);
    Route::get('/bdm/get-person-data/{id}' , [LicenseController::class, 'getPersonData']);
    Route::post('/bdm/update-person/{id}' , [LicenseController::class, 'updatePerson']);
    Route::post('/bdm/store-structures/{id}' , [StructuresController::class, 'storeStructures']);
    Route::get('/bdm/pre-data-structures/list/{id}' , [StructuresController::class, 'preDataStructures']);
    Route::get('/bdm/send-dossier-bill/{id}' , [LicenseController::class, 'sendDossierBill']);
    Route::post('/bdm/publish-dossier-bill/{id}' , [LicenseController::class, 'publishDossierBill']);
});


Route::prefix('v1')->group(function () {
    Route::get('/bdm/license-types/list' , [LicenseController::class, 'licenseTypesList']);
    Route::post('/bdm/licenses/create' , [LicenseController::class, 'create']);
    Route::post('/bdm/engineer/request' , [EngineerController::class, 'requestEngineer']);
    Route::get('/bdm/engineers-request/pre-data' , [EngineerController::class, 'engineersRequestPreData']);
});
