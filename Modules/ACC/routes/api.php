<?php

use Illuminate\Support\Facades\Route;
use Modules\ACC\app\Http\Controllers\ACCController;
use Modules\ACC\app\Http\Controllers\AccountsController;
use Modules\ACC\app\Http\Controllers\DocumentController;
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

    Route::get('/acc/fiscal-year/list', [DocumentController::class, 'fiscalYearList']);


    Route::post('/acc/villages-to-import', [ACCController::class, 'getOunitsToImport']);

    Route::post('/acc/finalize-import', [ACCController::class, 'convertToNewAccount']);

    Route::get('/acc/old-data/convert', [ACCController::class, 'getConfirmationForOldData']);

    Route::post('/acc/import/docs', [ACCController::class, 'importDocs']);

    Route::post('/acc/import/budget-items', [ACCController::class, 'importBudgets']);

    Route::post('/acc/accounts/list-by-type', [AccountsController::class, 'accountIndexByType']);

    Route::post('/acc/documents/purge-old-doc', [DocumentController::class, 'purgeDocuments']);

    Route::put('/acc/documents/old-doc/edit/{id}', [DocumentController::class, 'updateOldDocument']);

    Route::post('/acc/documents/rearrange-doc-numbers', [DocumentController::class, 'rearrangeDocuments']);


});

Route::middleware([])->prefix('v1')->name('api.')->group(function () {
    Route::post('/acc/account-swap', [ACCController::class, 'getOldDataToConvert']);


    Route::post('/acc/swap/accounts', [ACCController::class, 'newActiveAccounts']);

    Route::post('/acc/convert-to-new-codes', [ACCController::class, 'setNewChainCodeToAccount']);

    Route::get('/acc/import-check', [ACCController::class, 'importDocChecker']);


});
Route::middleware(['auth:api', 'route'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/acc/accounts/list', [AccountsController::class, 'index']);

    Route::post('/acc/accounts/add', [AccountsController::class, 'store']);

    Route::get('/acc/accounts/add', [AccountsController::class, 'getAddAccountBaseInfo']);

    Route::delete('/acc/accounts/delete/{id}', [AccountsController::class, 'deleteAccount']);

    Route::put('/acc/accounts/edit/{id}', [AccountsController::class, 'update']);

    Route::post('/acc/documents/opening-doc', [DocumentController::class, 'createOpeningDocument']);

    Route::post('/acc/documents/close-temp-docs', [DocumentController::class, 'createClosingTemporaryDocument']);

    Route::post('/acc/documents/close-year-docs', [DocumentController::class, 'createClosingDocument']);

    Route::post('/acc/documents/close-temp-docs/add', [DocumentController::class, 'insertClosingTemporaryDocument']);

    Route::post('/acc/documents/close-year-docs/add', [DocumentController::class, 'insertClosingTemporaryDocument']);

    Route::post('/acc/documents/opening-doc/add', [DocumentController::class, 'insertClosingTemporaryDocument']);

    Route::get('/acc/fiscal-year/current', [DocumentController::class, 'currentFiscalYearSummary']);

    Route::post('/acc/documents/add', [DocumentController::class, 'store']);

    Route::get('/acc/documents/add', [DocumentController::class, 'addDocumentBaseInfo']);

    Route::get('/acc/documents/list', [DocumentController::class, 'index']);

    Route::post('/acc/documents/archive/list', [DocumentController::class, 'archiveIndex']);

    Route::get('/acc/documents/{ounitID}/doc/{id}', [DocumentController::class, 'show']);

    Route::put('/acc/documents/edit/{id}', [DocumentController::class, 'update']);

    Route::put('/acc/documents/confirmed-status', [DocumentController::class, 'setConfirmedStatusTODocument']);

    Route::put('/acc/documents/draft-status', [DocumentController::class, 'setDraftStatusTODocument']);

    Route::delete('/acc/documents/delete-status', [DocumentController::class, 'setDeleteStatusTODocument']);

    Route::delete('/acc/cheque/free-cheque', [DocumentController::class, 'resetChequeAndFreeByArticle']);

    Route::post('/acc/documents/balance-sheet-report', [DocumentController::class, 'financialBalanceReport']);

    Route::post('/acc/accounts/person-acc/check', [AccountsController::class, 'personExistenceAndHasAccount']);

    Route::post('/acc/accounts/person-acc/add', [AccountsController::class, 'storeCreditAccount']);

    Route::post('/acc/documents/bookkeeping-report', [AccountsController::class, 'accountUsageReport']);

    Route::post('/acc/accounts/acc-remaining-balance', [AccountsController::class, 'accountRemainingValue']);

    Route::post('/acc/documents/mass-gov-dep', [DocumentController::class, 'bulkInsertDocsForOunits']);

    Route::get('/acc/ounit-banks/list', [DocumentController::class, 'ounitsWithBankAccounts']);

});
