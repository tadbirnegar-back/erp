<?php

use Illuminate\Support\Facades\Route;
use Modules\BNK\app\Http\Controllers\BNKController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {
    Route::resource('bnk', BNKController::class)->names('bnk');
});
