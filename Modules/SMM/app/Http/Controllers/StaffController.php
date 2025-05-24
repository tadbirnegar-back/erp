<?php

namespace Modules\SMM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ACMS\app\Http\Enums\AccountantScriptTypeEnum;
use Modules\HRMS\app\Http\Traits\EmployeeTrait;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Resources\RecruitmentScriptContractResource;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\SMM\app\Resources\OunitEmployeesResource;
use Modules\SMM\app\Traits\StaffTrait;

class StaffController extends Controller
{
    use StaffTrait, EmployeeTrait;

    public function FinancialManagerIndex(Request $request)
    {
        $user = \Auth::user();
        $data = $request->all();
        $recruitmentScripts = $user
            ->activeRecruitmentScripts()
            ->joinRelationship('scriptType', function ($query) {
                $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
            })
            ->get(['organization_unit_id'])->pluck('organization_unit_id')->flatten(1);
        $result = $this->employeesListForContract($data, $recruitmentScripts->toArray());

        return OunitEmployeesResource::collection($result);
    }

    public function getContractPreview(Request $request)
    {
        $data = $request->all();

        $validator = \Validator::make($data, [
            'rsID' => ['required', 'integer']
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $script = RecruitmentScript::with(
            [
                'person' => function ($query) {
                    $query->with(['natural', 'isar', 'militaryService' => function ($query) {
                        $query->with(['exemptionType', 'militaryServiceStatus']);
                    }])
                        ->finalPersonStatus()
                        ->select([
                            'persons.*',
                            'ps.name as person_status_name'
                        ]);

                },
                'ounit.ancestors' => function ($query) {
                    $query->where('unitable_type', '!=', TownOfc::class);
                },
                'latestEducationRecord.levelOfEducation',
                'position',
                'scriptType',
                'hireType',
            ]
        )
            ->withCount('heirs')
            ->find($data['rsID']);
        $agents = $this->getScriptAgentCombos($script);

        $script->setAttribute('agents', $agents->groupBy('sa_type_title'));

        return RecruitmentScriptContractResource::make($script);
    }


}
