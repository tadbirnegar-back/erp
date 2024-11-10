<?php

use Illuminate\Support\Facades\Route;
use Modules\EMS\app\Http\Controllers\EMSController;
use Modules\EMS\app\Http\Controllers\EnactmentController;

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

    Route::post('mes/enactment/add-by-board', [EnactmentController::class, 'store']);

    Route::post('mes/enactment/add-by-secretary', [EnactmentController::class, 'store']);

    Route::post('mes/pbs-enactments/list', [EnactmentController::class, 'indexSecretary']);

    Route::post('mes/pbc-enactments/list', [EnactmentController::class, 'indexHeyaat']);

    Route::post('mes/all-enactments/list', [EnactmentController::class, 'indexArchive']);

    Route::post('mes/enactments/{id}', [EnactmentController::class, 'show']);

    Route::post('mes/enactments/approve/{id}', [EnactmentController::class, 'enactmentApproval']);

    Route::post('mes/enactments/decline/{id}', [EnactmentController::class, 'enactmentDenial']);

    Route::post('mes/enactments/deny/{id}', [EnactmentController::class, 'enactmentInconsistency']);

    Route::post('mes/enactments/accept/{id}', [EnactmentController::class, 'enactmentNoInconsistency']);

    Route::post('mes/setting/secretary', [EMSController::class, 'getHeyaatMembers']);

    Route::put('mes/setting/secretary', [EMSController::class, 'updateHeyaatMembers']);

    Route::post('mes/settings/district-members/list', [EMSController::class, 'getDistrictOfcsWithMembersCount']);

    Route::post('mes/settings/district-members', [EMSController::class, 'getHeyaatMembersByOunit']);

    Route::put('mes/settings/district-members', [EMSController::class, 'updateHeyaatMembersByOunit']);

    Route::post('mes/settings/auto-moghayerat', [EMSController::class, 'getAutoNoMoghayeratSettings']);

    Route::put('mes/settings/auto-moghayerat', [EMSController::class, 'updateAutoMoghayeratSettings']);

    Route::post('mes/settings/enactment-titles/list', [EMSController::class, 'getEnactmentTitlesIndex']);

    Route::put('mes/settings/enactment-titles/{id}', [EMSController::class, 'updateEnactmentTitle']);

    Route::post('mes/settings/enactment-titles/add', [EMSController::class, 'storeEnactmentTitle']);

    Route::delete('mes/settings/enactment-titles/{id}', [EMSController::class, 'destroyEnactmentTitle']);

    Route::post('mes/reports/member', [\Modules\EMS\app\Http\Controllers\ReportsController::class, 'myEnactmentsReport']);

    Route::post('mes/reports/my-report', [\Modules\EMS\app\Http\Controllers\ReportsController::class, 'myEnactmentsReport']);

    Route::post('mes/reports/district-report', [\Modules\EMS\app\Http\Controllers\ReportsController::class, 'districtEnactmentReport']);

});

Route::middleware(['auth:api'])->prefix('v1')->group(function () {

    Route::get('mes/enactment/add-by-board', [EMSController::class, 'addBaseInfo']);

    Route::post('mes/ounit-villages/search', [EnactmentController::class, 'getMyVillagesToAddEnactment']);


});

Route::middleware([])->prefix('v1')->group(function () {

    Route::post('mes/mr/list', [EMSController::class, 'getMrList']);

    Route::post('mes/board/register', [EMSController::class, 'registerHetaatMember']);


});

Route::get('/MakeMeeting', [EMSController::class, 'makemakemake']);
