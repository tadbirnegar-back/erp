<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\TownOfc;

class HierarchyService
{

    use ApprovingListTrait;

    public static function procedureIdentifier(int $optionID = null)
    {
        return null;
    }

    public static function generateApprovers(?int $optionID, RecruitmentScript $script)
    {
        $oUnit = $script->load(['organizationUnit.ancestorsAndSelf' => function ($query) {
            $query
                ->where('unitable_type', '!=', TownOfc::class)
                ->where('head_id', '!=', null)
//                ->whereDepth('!=',1)
                ->with('head');

        }])->organizationUnit;


        $result = $oUnit->ancestorsAndSelf->map(function (OrganizationUnit $ou, $key) use ($script) {

            return [
                'assignedUserID' => $ou->head?->id,
                'scriptID' => $script->id,

            ];
        });

        return $result->toArray();
    }
}
