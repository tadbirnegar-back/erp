<?php

use Illuminate\Support\Facades\Route;
use Modules\ACC\app\Http\Controllers\AccountsController;
use Modules\ACMS\app\Http\Controllers\ACMSController;

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
    Route::get('/acc/my-villages/list', [ACMSController::class, 'dispatchedCircularsForMyVillage']);

});
Route::middleware(['auth:api', 'route'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/acc/accounts/list', [AccountsController::class, 'index']);

    Route::post('/acc/accounts/add', [AccountsController::class, 'store']);

    Route::get('/acc/accounts/add', [AccountsController::class, 'getAddAccountBaseInfo']);

    Route::delete('/acc/accounts/delete/{id}', [AccountsController::class, 'deleteAccount']);

    Route::put('/acc/accounts/edit/{id}', [AccountsController::class, 'update']);
});
