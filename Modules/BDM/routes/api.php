<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\BDM\app\Http\Controllers\LicenseController;

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

Route::prefix('v1')->group(function () {
    Route::get('/bdm/license-types/list' , [LicenseController::class, 'licenseTypesList']);
    Route::get('/bdm/license-ownership-types/list' , [LicenseController::class, 'licenseOwnershipTypesList']);
    Route::get('/bdm/transfer-types/list' , [LicenseController::class, 'transferTypesList']);
    Route::post('/bdm/licenses/create' , [LicenseController::class, 'create']);
});
