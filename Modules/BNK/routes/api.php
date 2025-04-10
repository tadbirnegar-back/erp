<?php

use Illuminate\Support\Facades\Route;
use Modules\ACC\app\Http\Controllers\AccountsController;
use Modules\BNK\app\Http\Controllers\BankAccountController;
use Modules\BNK\app\Http\Controllers\CardController;
use Modules\BNK\app\Http\Controllers\ChequeController;

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

Route::middleware(['auth:api', 'route'])->prefix('v1')->name('api.')->group(function () {
    Route::get('/bnk/bank-accounts/list', [BankAccountController::class, 'index']);

    Route::post('/bnk/bank-accounts/add', [BankAccountController::class, 'store']);

    Route::get('/bnk/bank-accounts/add', [BankAccountController::class, 'addBaseInfo']);

    Route::get('/bnk/bank-accounts/{id}', [BankAccountController::class, 'show']);

    Route::put('/bnk/bank-accounts/edit/{id}', [BankAccountController::class, 'update']);

    Route::get('/bnk/bank-accounts/edit/{id}', [BankAccountController::class, 'edit']);

    Route::delete('/bnk/bank-accounts/delete/{id}', [BankAccountController::class, 'destroy']);

    Route::post('/bnk/cheque-book/add', [ChequeController::class, 'store']);

    Route::post('/bnk/get-first-available-cheque', [AccountsController::class, 'getFirstEmptyCheck']);

    Route::put('/bnk/cheque-book/edit/{id}', [ChequeController::class, 'update']);

    Route::delete('/bnk/cheque-book/delete', [ChequeController::class, 'destroyChequeBook']);

    Route::post('/bnk/card/add', [CardController::class, 'store']);

    Route::put('/bnk/card/edit/{id}', [CardController::class, 'update']);
});
