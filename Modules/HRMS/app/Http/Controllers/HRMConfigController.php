<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\AAA\app\Models\Role;
use Modules\HRMS\app\Http\Enums\FormulaEnum;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\HRMS\app\Http\Enums\ProceduresEnum;
use Modules\HRMS\app\Http\Enums\ScriptTypeOriginEnum;
use Modules\HRMS\app\Http\Traits\ConfirmationTypeTrait;
use Modules\HRMS\app\Http\Traits\HireTypeTrait;
use Modules\HRMS\app\Http\Traits\JobTrait;
use Modules\HRMS\app\Http\Traits\LevelTrait;
use Modules\HRMS\app\Http\Traits\PositionTrait;
use Modules\HRMS\app\Http\Traits\ScriptAgentTrait;
use Modules\HRMS\app\Http\Traits\ScriptAgentTypesTrait;
use Modules\HRMS\app\Http\Traits\ScriptTypeTrait;
use Modules\HRMS\app\Http\Traits\SkillTrait;
use Modules\HRMS\app\Models\ContractType;
use Modules\HRMS\app\Models\Employee;

class HRMConfigController extends Controller
{
    use ScriptTypeTrait, ConfirmationTypeTrait, ScriptAgentTypesTrait, HireTypeTrait, ScriptAgentTrait, SkillTrait, LevelTrait
        , JobTrait, PositionTrait;

    public function configList()
    {
        $config['script_types'] = $this->getListOfScriptTypes();

//        $config['script_types_issue_time']
        $config['ounitCategories'] = OunitCategoryEnum::ounitCatList();
        $config['origins'] = ScriptTypeOriginEnum::getAllLabelsAndValues();
        $config['employee_status_list'] = Employee::GetAllStatuses();

        $config['confirmation_types'] = ProceduresEnum::proceduresList();

        $config['script_agent_types'] = $this->getListOfScriptAgentTypes();

        $config['hire_types'] = $this->getAllHireTypes();

        $config['value_types'] = [
            ['title' => "مقدار ثابت", 'id' => 1],
            ['title' => "بر اساس فرمول", 'id' => 2],
        ];
        $config['formula_list'] = FormulaEnum::FormulaList();

        $config['script_agents'] = $this->getListOfScriptAgents();

        $config['contract_types'] = ContractType::all();

        $config['skills'] = $this->skillIndex();
        $config['levels'] = $this->levelIndex();
        $config['jobs'] = $this->getListOfJobs();
        $config['positions'] = $this->positionIndex();
        $config['rolesList'] = Role::all();

        return response()->json($config);

    }
}
