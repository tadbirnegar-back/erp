<?php

use Illuminate\Support\Facades\Route;
use Modules\AAA\app\Http\Controllers\Auth\LoginControllerV2;

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
    Route::post('/users/add', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'register'])->middleware('auth:api', 'route');
    Route::post('/users/list', [\Modules\AAA\app\Http\Controllers\UserController::class, 'index'])->middleware('auth:api', 'route');
    Route::delete('/users/delete/{id}', [\Modules\AAA\app\Http\Controllers\UserController::class, 'destroy'])->middleware('auth:api', 'route');
    Route::post('/users/update/{id}', [\Modules\AAA\app\Http\Controllers\UserController::class, 'show'])->middleware('auth:api', 'route')->name('user.edit');
    Route::put('/users/update/{id}', [\Modules\AAA\app\Http\Controllers\UserController::class, 'updaFte'])->middleware('auth:api', 'route');
    Route::post('/check', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'userMobileExists']);
    Route::post('users/logout', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'logout'])->middleware('auth:api');
    Route::post('/login', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'loginGrant'])->name('login.login-grant');
    Route::post('/refresh', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'refreshToken']);
    Route::post('/otp/request', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'generateOtp']);
    Route::post('/otp/verify', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'otpLogin']);
    Route::post('/users/permissions/list', [\Modules\AAA\app\Http\Controllers\PermissionController::class, 'userPermissionList'])->middleware('auth:api');
    Route::post('/users/roles/add', [\Modules\AAA\app\Http\Controllers\RoleController::class, 'store'])->middleware(['auth:api', 'route']);
    Route::post('/users/natural/search', [\Modules\AAA\app\Http\Controllers\Auth\LoginController::class, 'isPersonUser'])->middleware(['auth:api']);
    Route::post('/users/roles/list', [\Modules\AAA\app\Http\Controllers\RoleController::class, 'index'])->middleware(['auth:api', 'route']);
    Route::post('/users/roles/{id}', [\Modules\AAA\app\Http\Controllers\RoleController::class, 'show'])->middleware(['auth:api', 'route']);
    Route::delete('/users/roles/delete/{id}', [\Modules\AAA\app\Http\Controllers\RoleController::class, 'destroy'])->middleware(['auth:api', 'route']);
    Route::put('/users/roles/update/{id}', [\Modules\AAA\app\Http\Controllers\RoleController::class, 'update'])->middleware(['auth:api', 'route']);
    Route::post('/users/roles/update/{id}', [\Modules\AAA\app\Http\Controllers\RoleController::class, 'show'])->middleware(['auth:api', 'route'])->name('role.edit');
    Route::post('/permissions/list', [\Modules\AAA\app\Http\Controllers\PermissionController::class, 'index'])->middleware('auth:api');
    Route::post('/users/view/{id}', [\Modules\AAA\app\Http\Controllers\UserController::class, 'show'])->middleware('auth:api', 'route');
    Route::post('/users/widgets/active', [\Modules\AAA\app\Http\Controllers\AAAController::class, 'activeWidgets'])->middleware('auth:api');


    Route::get('/widget/profile', [\Modules\AAA\app\Http\Widgets\UserWidgets::class, 'getUserInfo']);

    Route::get('/widget/calendar', [\Modules\AAA\app\Http\Widgets\UserWidgets::class, 'calendar']);
    Route::get('/widget/village_ofc', [\Modules\AAA\app\Http\Widgets\UserWidgets::class, 'userOunits']);

    //settings route
    Route::post('/setting/widgets', [\Modules\AAA\app\Http\Controllers\AAAController::class, 'widgets'])->middleware('auth:api');

    Route::put('/setting/widgets', [\Modules\WidgetsMS\app\Http\Controllers\WidgetsMSController::class, 'update'])->middleware('auth:api');

    Route::post('/setting/profile', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'currentPersonShow'])->middleware('auth:api');

    Route::put('/setting/profile', [\Modules\AAA\app\Http\Controllers\UserController::class, 'updateUserInfo'])->middleware('auth:api');

    Route::put('/setting/security', [\Modules\AAA\app\Http\Controllers\UserController::class, 'updatePassword'])->middleware('auth:api');

});

Route::prefix('v2')->group(function () {
    Route::post('/login', [LoginControllerV2::class, 'getToken']);
    Route::post('/refresh-token', [LoginControllerV2::class, 'refreshToken']);

});


Route::middleware(['auth:api'])->prefix('v2')->group(function () {
    Route::get('/get-permissions', [LoginControllerV2::class, 'getPermission']);
    Route::get('/get-user-info', [LoginControllerV2::class, 'getUserInfo']);
    Route::get('/check-payed', [LoginControllerV2::class, 'checkPayed']);
    Route::post('/login-with-otp', [LoginControllerV2::class, 'loginWithOtp']);
});
