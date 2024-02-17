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
    Route::post('/check', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'userMobileExists']);
    Route::post('/logout', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'logout'])->middleware('auth:api');
    Route::post('/login', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'loginGrant'])->name('login.login-grant');
    Route::post('/refresh', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'refreshToken']);
    Route::post('/user/permissions/list', [\Modules\AAA\app\Http\Controllers\PermissionController::class, 'userPermissionList'])->middleware('auth:api');
    Route::post('/users/roles/add', [\Modules\AAA\app\Http\Controllers\RoleController::class, 'store'])->middleware(['auth:api','route']);
    Route::post('/users/roles/list', [\Modules\AAA\app\Http\Controllers\RoleController::class, 'index'])->middleware(['auth:api','route']);
    Route::post('/users/roles/{id}', [\Modules\AAA\app\Http\Controllers\RoleController::class, 'show'])->middleware(['auth:api','route']);
    Route::delete('/users/roles/delete/{id}', [\Modules\AAA\app\Http\Controllers\RoleController::class, 'destroy'])->middleware(['auth:api','route']);
    Route::put('/users/roles/update/{id}', [\Modules\AAA\app\Http\Controllers\RoleController::class, 'update'])->middleware(['auth:api','route']);
    Route::post('/users/roles/update/{id}', [\Modules\AAA\app\Http\Controllers\RoleController::class, 'show'])->middleware(['auth:api','route'])->name('role.edit');
    Route::post('/permissions/list', [\Modules\AAA\app\Http\Controllers\PermissionController::class, 'index'])->middleware('auth:api');
});

