<?php

namespace Modules\SMM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ACMS\app\Http\Enums\AccountantScriptTypeEnum;
use Modules\SMM\app\Traits\StaffTrait;

class StaffController extends Controller
{
    use StaffTrait;

    public function FinancialManagerIndex(Request $request): JsonResponse
    {
        $user = \Auth::user();
        $data = $request->all();
        $recruitmentScripts = $user
            ->activeRecruitmentScripts()
            ->joinRelationship('scriptType', function ($query) {
                $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
            })
            ->get(['organization_unit_id'])->pluck('organization_unit_id')->flatten(1);
        $result = $this->employeesListForContract($data, $recruitmentScripts);
        return response()->json($result);
    }


}
