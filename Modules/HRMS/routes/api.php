<?php

use Illuminate\Support\Facades\Route;
use Modules\HRMS\app\Http\Controllers\ApprovingListController;
use Modules\HRMS\app\Http\Controllers\EmployeeController;
use Modules\HRMS\app\Http\Controllers\HireTypeController;
use Modules\HRMS\app\Http\Controllers\HRMConfigController;
use Modules\HRMS\app\Http\Controllers\JobController;
use Modules\HRMS\app\Http\Controllers\LevelController;
use Modules\HRMS\app\Http\Controllers\NewScriptController;
use Modules\HRMS\app\Http\Controllers\PositionController;
use Modules\HRMS\app\Http\Controllers\RecruitmentScriptController;
use Modules\HRMS\app\Http\Controllers\ScriptAgentTypeController;
use Modules\HRMS\app\Http\Controllers\SkillController;

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
    Route::post('/hrm/employee/add', [EmployeeController::class, 'store']);
    Route::post('/hrm/employee/list', [EmployeeController::class, 'index']);
    Route::get('/hrm/employee/list/filter', [EmployeeController::class, 'employeeListFilter']);
    Route::get('/hrm/setting', [HRMConfigController::class, 'configList']);
    Route::post('/hrm/erc/list', [RecruitmentScriptController::class, 'indexExpiredScripts']);

});

Route::middleware([])->prefix('v1')->name('api.')->group(function () {
    Route::post('/employee/natural/search', [EmployeeController::class, 'findPersonToInsertAsEmployee'])->middleware('auth:api');
    Route::post('/employee/national-code/search', [EmployeeController::class, 'findPersonToInsertAsEmployee']);
    Route::post('/recruitment/list/state_ofc', [RecruitmentScriptController::class, 'stateOfcs']);
    Route::post('/recruitment/list/city_ofc', [RecruitmentScriptController::class, 'cityOfcs']);
    Route::post('/recruitment/list/district_ofc', [RecruitmentScriptController::class, 'districtOfcs']);
    Route::post('/recruitment/list/town_ofc', [RecruitmentScriptController::class, 'townOfcs']);
    Route::post('/recruitment/list/village_ofc', [RecruitmentScriptController::class, 'villageOfcs']);
    Route::post('/hrm/ounit/positions/list', [PositionController::class, 'getByOrganizationUnit']);
    Route::get('/hrm/employee/add', [EmployeeController::class, 'addEmployeeBaseInfo'])->middleware('auth:api');
    Route::post('/hrm/education-levels/list', [\Modules\HRMS\app\Http\Controllers\LevelsOfEducationController::class, 'index']);
    Route::post('/hrm/register/dehyar', [EmployeeController::class, 'registerDehyar']);
    Route::post('/hrm/register/sarparast', [EmployeeController::class, 'registerSarparast']);

});

Route::middleware(['auth:api'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/hrm/levels/add', [LevelController::class, 'store']);
    Route::post('/hrm/levels/list', [LevelController::class, 'index']);
    Route::post('/hrm/levels/{id}', [LevelController::class, 'show']);
    Route::post('/hrm/levels/update/{id}', [LevelController::class, 'show']);
    Route::put('/hrm/levels/update/{id}', [LevelController::class, 'update']);
    Route::delete('/hrm/levels/delete/{id}', [LevelController::class, 'destroy']);


    Route::post('/hrm/positions/add', [PositionController::class, 'store']);
    Route::post('/hrm/positions/list', [PositionController::class, 'index']);
    Route::post('/hrm/positions/{id}', [PositionController::class, 'show']);
    Route::post('/hrm/positions/update/{id}', [PositionController::class, 'show']);
    Route::put('/hrm/positions/update/{id}', [PositionController::class, 'update']);
    Route::delete('/hrm/positions/delete/{id}', [PositionController::class, 'destroy']);

    Route::post('/hrm/new/req/script', [NewScriptController::class, 'indexVillage']);
    Route::post('/hrm/request-new-Supervisor', [NewScriptController::class, 'storeSarParast']);
    Route::get('/hrm/district/list', [NewScriptController::class, 'districtsDropDown']);
    Route::post('/hrm/request-new-heayt', [NewScriptController::class, 'storeheyat']);

    Route::post('/hrm/skills/add', [SkillController::class, 'store']);
    Route::post('/hrm/skills/list', [SkillController::class, 'index']);
    Route::post('/hrm/skills/{id}', [SkillController::class, 'show']);
    Route::post('/hrm/skills/update/{id}', [SkillController::class, 'show']);
    Route::put('/hrm/skills/update/{id}', [SkillController::class, 'update']);
    Route::delete('/hrm/skills/delete/{id}', [SkillController::class, 'destroy']);
    Route::post('/hrm/dehyar/request', [RecruitmentScriptController::class, 'getMyVillageScripts']);

    Route::get('/hrm/rc/add', [EmployeeController::class, 'addEmployeeBaseInfo']);
});

Route::middleware(['auth:api', 'route'])->prefix('v1')->name('api.')->group(function () {

    Route::post('/hrm/script-agent-type/add', [ScriptAgentTypeController::class, 'store']);
    Route::put('/hrm/script-agent-type/update/{id}', [ScriptAgentTypeController::class, 'update']);

    Route::delete('/hrm/script-agent-type/delete/{id}', [ScriptAgentTypeController::class, 'destroy']);


    Route::post('/hrm/jobs/add', [JobController::class, 'store']);
    Route::put('/hrm/jobs/update/{id}', [JobController::class, 'update']);

    Route::delete('/hrm/jobs/delete/{id}', [JobController::class, 'destroy']);


    Route::post('/hrm/hire-types/add', [HireTypeController::class, 'store']);

    Route::put('/hrm/hire-types/update/{id}', [HireTypeController::class, 'update']);

    Route::delete('/hrm/hire-types/delete/{id}', [HireTypeController::class, 'destroy']);


    Route::post('/hrm/script-types/add', [\Modules\HRMS\app\Http\Controllers\ScriptTypeController::class, 'store']);
    Route::put('/hrm/script-types/update/{id}', [\Modules\HRMS\app\Http\Controllers\ScriptTypeController::class, 'update']);

    Route::delete('/hrm/script-types/delete/{id}', [\Modules\HRMS\app\Http\Controllers\ScriptTypeController::class, 'destroy']);


    Route::post('/hrm/script-agents/add', [\Modules\HRMS\app\Http\Controllers\ScriptAgentController::class, 'store']);
    Route::put('/hrm/script-agents/update/{id}', [\Modules\HRMS\app\Http\Controllers\ScriptAgentController::class, 'update']);

    Route::delete('/hrm/script-agents/delete/{id}', [\Modules\HRMS\app\Http\Controllers\ScriptAgentController::class, 'destroy']);


    Route::post('/hrm/employee/script-combos/', [EmployeeController::class, 'agentCombos']);

    Route::post('/hrm/employee/script-types/', [EmployeeController::class, 'employeeScriptTypes']);

    Route::post('/hrm/rc/list', [RecruitmentScriptController::class, 'index'])->middleware(['auth:api']);

    Route::post('/hrm/prc/list', [RecruitmentScriptController::class, 'pendingApprovingIndex'])->middleware(['auth:api']);

    Route::post('/hrm/prc/{id}', [ApprovingListController::class, 'showScriptWithApproves'])->middleware(['auth:api']);

    Route::post('/hrm/rc/{id}', [RecruitmentScriptController::class, 'recruitmentScriptShow'])->middleware(['auth:api']);

    Route::put('/hrm/rc/grant/{id}', [ApprovingListController::class, 'approveScriptByUser'])->middleware(['auth:api']);

    Route::put('/hrm/rc/decline/{id}', [ApprovingListController::class, 'declineScriptByUser'])->middleware(['auth:api']);

    Route::put('/hrm/rc/cancel/{id}', [RecruitmentScriptController::class, 'cancelRscript'])->middleware(['auth:api']);

    Route::put('/hrm/rc/renew/{id}', [RecruitmentScriptController::class, 'renewScript'])->middleware(['auth:api']);

//    Route::put('/hrm/rc/terminate/{id}', [RecruitmentScriptController::class, 'terminateRscript'])->middleware(['auth:api']);

    Route::put('/hrm/rc/service-end/{id}', [RecruitmentScriptController::class, 'endOfServiceRscript'])->middleware(['auth:api']);

//    Route::put('/hrm/rc/cancel/{id}', [\Modules\HRMS\app\Http\Controllers\RecruitmentScriptController::class, 'cancelRscript'])->middleware(['auth:api']);

//    Route::put('/hrm/rc/renew/{id}', [\Modules\HRMS\app\Http\Controllers\RecruitmentScriptController::class, 'renewScript'])->middleware(['auth:api']);

//    Route::put('/hrm/rc/terminate/{id}', [\Modules\HRMS\app\Http\Controllers\RecruitmentScriptController::class, 'terminateRscript'])->middleware(['auth:api']);

    Route::put('/hrm/rc/service-end/{id}', [RecruitmentScriptController::class, 'endOfServiceRscript'])->middleware(['auth:api']);


    Route::post('/hrm/rc/insert/add', [RecruitmentScriptController::class, 'store']);


    Route::post('/hrm/isar-types/list', [EmployeeController::class, 'isarsStatusesIndex']);

    Route::post('/hrm/relative-types/list', [EmployeeController::class, 'relativeTypesIndex']);


    Route::post('/hrm/employee/verify', [EmployeeController::class, 'verifyEmployeeForScript']);


    Route::post('/hrm/rc/reissue/{id}', [RecruitmentScriptController::class, 'RenewRecruitmentScript']);

    Route::post('/hrm/rc/manager-reject/{id}', [RecruitmentScriptController::class, 'RejectRecruitmentScript']);

    Route::post('/hrm/rc/manager-approve/{id}', [RecruitmentScriptController::class, 'approveRecruitmentScript']);

    Route::post('/hrm/village/search-by-abadi-code', [RecruitmentScriptController::class, 'getVillageOfcByAbadiCode']);

    Route::post('/hrm/request-new-village', [RecruitmentScriptController::class, 'addNewScriptForDehyar']);

    Route::get('/hrm/ptprc/list', [RecruitmentScriptController::class, 'ptpIndex']);

    Route::get('/hrm/rc/ptp/{id}', [RecruitmentScriptController::class, 'ptpShow']);

    Route::post('/hrm/rc/ptp/terminate/{id}', [RecruitmentScriptController::class, 'ptpTerminate']);

});
