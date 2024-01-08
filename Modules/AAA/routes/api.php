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
Route::prefix('v1')->group(function () {
    Route::post('/register', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'register']);
    Route::post('/logout', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'logout'])->middleware('auth:api');
    Route::post('/login', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'loginGrant'])->name('login.login-grant');
    Route::post('/refresh', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'refreshToken']);
});
//Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
//    Route::get('aaa', fn (Request $request) => $request->user())->name('aaa');
//});
