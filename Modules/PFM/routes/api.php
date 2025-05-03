<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\PFM\app\Http\Controllers\ApplicationsController;
use Modules\PFM\app\Http\Controllers\BookletController;
use Modules\PFM\app\Http\Controllers\CircularController;
use Modules\PFM\app\Http\Controllers\LevyItemsController;
use Modules\PFM\app\Http\Controllers\BillsController;

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
    //End points of Karshenase ostan dari
    Route::post('/pfm/circular/store', [CircularController::class, 'store']);
    Route::post('/pfm/circulars/list', [CircularController::class, 'index']);
    Route::get('/pfm/circular/show/{id}', [CircularController::class, 'show']);
    Route::post('/pfm/circular/update/{id}', [CircularController::class, 'update']);
    Route::get('/pfm/circular/update/{id}', [CircularController::class, 'showForUpdate']);
    Route::post('/pfm/circulars/publish/{id}', [CircularController::class, 'generateBooklets']);
    Route::get('/pfm/circular/delete/{id}', [CircularController::class, 'delete']);
    Route::post('/pfm/levy-items/store/{id}', [LevyItemsController::class, 'store']);
    Route::get('/pfm/levy-items/delete/{id}', [LevyItemsController::class, 'delete']);
    Route::get('/pfm/levy-items/index/{id}', [LevyItemsController::class, 'index']);
    Route::post('/pfm/levy-items/update/{id}', [LevyItemsController::class, 'update']);

    //End points of masoule fani
    Route::post('/pfm/booklets/list', [BookletController::class, 'index']);
    Route::post('/pfm/booklets/list/this-year', [BookletController::class, 'indexThisYear']);
    Route::get('/pfm/booklet/show/{id}', [BookletController::class, 'show']);
    //Tariffs
    Route::post('/pfm/booklet-items/{id}', [BookletController::class, 'showItems']);
    Route::get('/pfm/booklet-prices/{id}', [BookletController::class, 'showPrices']);
    Route::post('/pfm/booklet-prices/{id}', [BookletController::class, 'storePrices']);
    Route::get('/pfm/booklet/submit/{id}', [BookletController::class, 'submitBooklet']);
    Route::post('/pfm/booklet/decline/{id}', [BookletController::class, 'declineBooklet']);
    Route::post('/pfm/booklet/tariff/store/{id}', [BookletController::class, 'store']);
    Route::post('/pfm/booklet-prices/store/with-out-app/{id}', [BookletController::class, 'storeWithoutApp']);

    // Bills
    Route::get('/pfm/bills/village-data', [BillsController::class, 'billsVillageData']);
    Route::get('/pfm/bills/bank-data/{id}', [BillsController::class, 'bankAccounts']);
    Route::post('/pfm/bills/booklet-data/{id}', [BillsController::class, 'BookletData']);
    Route::get('/pfm/bills/export/bill/{id}', [BillsController::class, 'leviesList']);
    Route::post('/pfm/bills/levy-items/index/{id}', [BillsController::class, 'levyItemsList']);
    Route::post('/pfm/bills/filled-data/index/{id}', [BillsController::class, 'getFilledData']);
    Route::post('/pfm/bills/check-national-code', [BillsController::class, 'checkNationalCode']);
    Route::post('/pfm/bills/send-bill', [BillsController::class, 'sendBill']);
    Route::post('/pfm/bills/list', [BillsController::class, 'billsList']);
    Route::get('/pfm/bills/show-bill/{id}', [BillsController::class, 'showBill']);
    Route::post('/pfm/bills/confirm-bill/{id}', [BillsController::class, 'confirmBill']);
    Route::post('/pfm/bills/cancel-bill/{id}' , [BillsController::class, 'cancelBill']);
});

