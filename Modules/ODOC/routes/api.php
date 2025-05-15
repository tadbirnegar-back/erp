<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\ODOC\app\Http\Controllers\ODOCController;

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
    Route::post('/odoc/document/create' , [ODOCController::class, 'createOdocDocument']);
});
