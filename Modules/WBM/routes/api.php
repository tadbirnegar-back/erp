<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\WBM\app\Http\Controllers\DossierWBMController;

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
});
Route::post('/wbm/works-for-engineers/list' , [DossierWBMController::class, 'listOfWorksForEngineers']);
