<?php

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


Route::middleware(['auth:api', 'route'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/hrm/employee/add', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'store']);
    Route::post('/hrm/employee/list', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'index']);
    Route::get('/hrm/employee/list/filter', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'employeeListFilter']);
    Route::get('/hrm/setting', [\Modules\HRMS\app\Http\Controllers\HRMConfigController::class, 'configList']);
//    Route::post('/products/merchandise/variants/list', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'index']);
//    Route::put('/products/merchandise/variants/update/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'update']);
//    Route::post('/products/merchandise/variants/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'show']);
//    Route::post('/products/merchandise/variants/update/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'show']);
//    Route::delete('/products/merchandise/variants/delete/{id}', [\Modules\ProductMS\app\Http\Controllers\VariantController::class, 'destroy']);
});

Route::middleware([])->prefix('v1')->name('api.')->group(function () {
    Route::post('/employee/natural/search', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'findPersonToInsertAsEmployee'])->middleware('auth:api');
    Route::post('/employee/national-code/search', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'findPersonToInsertAsEmployee']);
    Route::post('/recruitment/list/state_ofc', [\Modules\HRMS\app\Http\Controllers\RecruitmentScriptController::class, 'stateOfcs']);
    Route::post('/recruitment/list/city_ofc', [\Modules\HRMS\app\Http\Controllers\RecruitmentScriptController::class, 'cityOfcs']);
    Route::post('/recruitment/list/district_ofc', [\Modules\HRMS\app\Http\Controllers\RecruitmentScriptController::class, 'districtOfcs']);
    Route::post('/recruitment/list/town_ofc', [\Modules\HRMS\app\Http\Controllers\RecruitmentScriptController::class, 'townOfcs']);
    Route::post('/recruitment/list/village_ofc', [\Modules\HRMS\app\Http\Controllers\RecruitmentScriptController::class, 'villageOfcs']);
    Route::post('/hrm/ounit/positions/list', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'getByOrganizationUnit']);
    Route::get('/hrm/employee/add', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'addEmployeeBaseInfo'])->middleware('auth:api');
    Route::post('/hrm/education-levels/list', [\Modules\HRMS\app\Http\Controllers\LevelsOfEducationController::class, 'index']);
    Route::post('/hrm/register/dehyar', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'registerDehyar']);

});

Route::middleware(['auth:api'])->prefix('v1')->name('api.')->group(function () {
    Route::post('/hrm/levels/add', [\Modules\HRMS\app\Http\Controllers\LevelController::class, 'store']);
    Route::post('/hrm/levels/list', [\Modules\HRMS\app\Http\Controllers\LevelController::class, 'index']);
    Route::post('/hrm/levels/{id}', [\Modules\HRMS\app\Http\Controllers\LevelController::class, 'show']);
    Route::post('/hrm/levels/update/{id}', [\Modules\HRMS\app\Http\Controllers\LevelController::class, 'show']);
    Route::put('/hrm/levels/update/{id}', [\Modules\HRMS\app\Http\Controllers\LevelController::class, 'update']);
    Route::delete('/hrm/levels/delete/{id}', [\Modules\HRMS\app\Http\Controllers\LevelController::class, 'destroy']);


    Route::post('/hrm/positions/add', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'store']);
    Route::post('/hrm/positions/list', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'index']);
    Route::post('/hrm/positions/{id}', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'show']);
    Route::post('/hrm/positions/update/{id}', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'show']);
    Route::put('/hrm/positions/update/{id}', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'update']);
    Route::delete('/hrm/positions/delete/{id}', [\Modules\HRMS\app\Http\Controllers\PositionController::class, 'destroy']);


    Route::post('/hrm/skills/add', [\Modules\HRMS\app\Http\Controllers\SkillController::class, 'store']);
    Route::post('/hrm/skills/list', [\Modules\HRMS\app\Http\Controllers\SkillController::class, 'index']);
    Route::post('/hrm/skills/{id}', [\Modules\HRMS\app\Http\Controllers\SkillController::class, 'show']);
    Route::post('/hrm/skills/update/{id}', [\Modules\HRMS\app\Http\Controllers\SkillController::class, 'show']);
    Route::put('/hrm/skills/update/{id}', [\Modules\HRMS\app\Http\Controllers\SkillController::class, 'update']);
    Route::delete('/hrm/skills/delete/{id}', [\Modules\HRMS\app\Http\Controllers\SkillController::class, 'destroy']);
});

Route::middleware(['auth:api'])->prefix('v1')->name('api.')->group(function () {

    Route::post('/hrm/script-agent-type/add', [\Modules\HRMS\app\Http\Controllers\ScriptAgentTypeController::class, 'store']);
    Route::put('/hrm/script-agent-type/update/{id}', [\Modules\HRMS\app\Http\Controllers\ScriptAgentTypeController::class, 'update']);

    Route::delete('/hrm/script-agent-type/delete/{id}', [\Modules\HRMS\app\Http\Controllers\ScriptAgentTypeController::class, 'destroy']);


    Route::post('/hrm/jobs/add', [\Modules\HRMS\app\Http\Controllers\JobController::class, 'store']);
    Route::put('/hrm/jobs/update/{id}', [\Modules\HRMS\app\Http\Controllers\JobController::class, 'update']);

    Route::delete('/hrm/jobs/delete/{id}', [\Modules\HRMS\app\Http\Controllers\JobController::class, 'destroy']);


    Route::post('/hrm/hire-types/add', [\Modules\HRMS\app\Http\Controllers\HireTypeController::class, 'store']);

    Route::put('/hrm/hire-types/update/{id}', [\Modules\HRMS\app\Http\Controllers\HireTypeController::class, 'update']);

    Route::delete('/hrm/hire-types/delete/{id}', [\Modules\HRMS\app\Http\Controllers\HireTypeController::class, 'destroy']);


    Route::post('/hrm/script-types/add', [\Modules\HRMS\app\Http\Controllers\ScriptTypeController::class, 'store']);
    Route::put('/hrm/script-types/update/{id}', [\Modules\HRMS\app\Http\Controllers\ScriptTypeController::class, 'update']);

    Route::delete('/hrm/script-types/delete/{id}', [\Modules\HRMS\app\Http\Controllers\ScriptTypeController::class, 'destroy']);


    Route::post('/hrm/script-agents/add', [\Modules\HRMS\app\Http\Controllers\ScriptAgentController::class, 'store']);
    Route::put('/hrm/script-agents/update/{id}', [\Modules\HRMS\app\Http\Controllers\ScriptAgentController::class, 'update']);

    Route::delete('/hrm/script-agents/delete/{id}', [\Modules\HRMS\app\Http\Controllers\ScriptAgentController::class, 'destroy']);


    Route::post('/hrm/employee/script-combos/', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'agentCombos']);

    Route::post('/hrm/employee/script-types/', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'employeeScriptTypes']);

    Route::post('/hrm/rc/list', [\Modules\HRMS\app\Http\Controllers\RecruitmentScriptController::class, 'index'])->middleware(['auth:api']);

    Route::post('/hrm/prc/list', [\Modules\HRMS\app\Http\Controllers\RecruitmentScriptController::class, 'pendingApprovingIndex'])->middleware(['auth:api']);

    Route::post('/hrm/prc/{id}', [\Modules\HRMS\app\Http\Controllers\ApprovingListController::class, 'showScriptWithApproves'])->middleware(['auth:api']);

    Route::put('/hrm/rc/grant/{id}', [\Modules\HRMS\app\Http\Controllers\ApprovingListController::class, 'approveScriptByUser'])->middleware(['auth:api']);

    Route::post('/hrm/rc/add', [\Modules\HRMS\app\Http\Controllers\RecruitmentScriptController::class, 'store']);

    Route::get('/hrm/rc/add', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'addEmployeeBaseInfo']);

    Route::post('/hrm/isar-types/list', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'isarsStatusesIndex']);

    Route::post('/hrm/relative-types/list', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'relativeTypesIndex']);


    Route::post('/hrm/employee/verify', [\Modules\HRMS\app\Http\Controllers\EmployeeController::class, 'verifyEmployeeForScript']);
});

