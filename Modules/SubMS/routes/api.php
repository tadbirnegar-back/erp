<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\SubMS\app\Http\Controllers\SubMSController;

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

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
});
Route::get('/subscribers/check-target', [SubMsController::class, 'checkUserIsTargeted']);
Route::post('/subscribers/pay', [SubMsController::class, 'paySubscription']);
