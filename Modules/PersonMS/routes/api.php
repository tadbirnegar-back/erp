<?php

use Illuminate\Support\Facades\Route;
use Modules\PersonMS\app\Http\Controllers\PersonLicenseController;

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
    Route::post('/person/natural/add', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalStore']);
    Route::post('/person/natural/search', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalExists']);
    Route::post('/person/legal/add', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'legalStore']);
    Route::post('/person/natural/list', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalIndex']);
    Route::post('/person/legal/list', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'legalIndex']);
    Route::delete('/person/natural/delete/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalDestroy']);
    Route::delete('/person/legal/delete/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'legalDestroy']);
    Route::put('/person/natural/update/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalPersonUpdate']);
    Route::put('/person/legal/update/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'legalUpdate']);
    Route::post('/person/natural/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalShow']);
    Route::post('/person/legal/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'legalShow']);
    Route::post('/person/natural/edit/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'naturalShow']);
    Route::post('/person/legal/edit/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'legalShow']);
});

Route::middleware([])->prefix('v1')->name('api.')->group(function () {

    Route::delete('/person/hard-delete/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'deleteUserAndPerson'])->middleware(['auth:api']);

    Route::post('/person/religions/list', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'religionIndex']);

    Route::post('/person/military-status/list', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'militaryStatusesIndex']);

    Route::post('/person/log/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'personShow']);

    Route::put('/person/user-data/update/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'personProfileUpdate']);

    Route::put('/person/personal-data/update/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'personalUpdate']);

    Route::put('/person/personnel-code/update/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'updatePersonnelInfo']);

    Route::put('/person/skills/add/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'storeSkillPerson']);

    Route::put('/person/skills/edit/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'updateSkillPerson']);

    Route::delete('/person/skills/delete/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'destroySkillPerson']);

    Route::put('/person/educations/add/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'storeEducationalRecordPerson']);

    Route::put('/person/educations/edit/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'updateEducationalRecordPerson']);

    Route::delete('/person/educations/delete/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'destroyEducationalRecordPerson']);

    Route::put('/person/course-record/add/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'storeCourseRecordPerson']);

    Route::put('/person/course-record/edit/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'updateCourseRecordPerson']);

    Route::delete('/person/course-record/delete/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'destroyCourseRecordPerson']);

    Route::put('/person/resume/add/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'storeResumePerson']);

    Route::put('/person/resume/edit/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'updateResumePerson']);

    Route::delete('/person/resume/delete/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'destroyResumePerson']);

    Route::put('/person/relative/add/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'storeRelativePerson']);

    Route::put('/person/relative/edit/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'updateRelativePerson']);

    Route::delete('/person/relative/delete/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'destroyRelativePerson']);

    Route::put('/person/military-service/add/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'storeMilitaryServicePerson']);

    Route::put('/person/isar/add/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'storeIsarPerson']);

    Route::put('/person/contact-data/update/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'contactInfoUpdate'])->middleware('auth:api');

});
Route::put('/person/contact-data/update/{id}', [\Modules\PersonMS\app\Http\Controllers\PersonMSController::class, 'contactInfoUpdate']);

Route::middleware(['auth:api'])->prefix('v2')->name('api.')->group(function () {

    Route::get('/person/info/summary', [PersonLicenseController::class, 'personInfoSummary']);

    Route::get('/person/info/get-personal-data', [PersonLicenseController::class, 'getPersonalData']);

    Route::get('/person/info/get-spouse-data', [PersonLicenseController::class, 'getSpouse']);

    Route::get('/person/info/get-children-data', [PersonLicenseController::class, 'getChildren']);

    Route::get('/person/info/get-isar-data', [PersonLicenseController::class, 'getIsar']);

    Route::get('/person/info/get-educational-records-data', [PersonLicenseController::class, 'getEducationalRecords']);

    Route::post('/person/check-national-code', [PersonLicenseController::class, 'checkPersonExistence']);


    Route::put('/person/info/update-personal-data', [PersonLicenseController::class, 'updatePersonalData']);

    Route::put('/person/info/add-spouse-data', [PersonLicenseController::class, 'storeSpouse']);

    Route::put('/person/info/add-children-data', [PersonLicenseController::class, 'storeChildren']);

    Route::put('/person/info/update-children-data', [PersonLicenseController::class, 'updateChild']);

    Route::put('/person/info/update-isar-data', [PersonLicenseController::class, 'updateIsar']);

    Route::put('/person/info/add-educational-records-data', [PersonLicenseController::class, 'insertEducationalRecord']);

    Route::put('/person/info/update-educational-records-data', [PersonLicenseController::class, 'updateEducationalRecord']);
});
