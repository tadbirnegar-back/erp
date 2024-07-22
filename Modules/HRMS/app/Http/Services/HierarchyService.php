<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\OUnitMS\app\Models\OrganizationUnit;

class HierarchyService
{

    use ApprovingListTrait;
    public static function procedureIdentifier(int $optionID=null)
    {
      return null;
}

    public static function generateApprovers(?int $optionID,RecruitmentScript $script)
    {
        $oUnit = OrganizationUnit::with('ancestorsAndSelf.head')->find($optionID);

        $currentUserPendingStatus = self::pendingForCurrentUserStatus();
        $pendingStatus = self::pendingStatus();

        $result = $oUnit->ancestorsAndSelf->map(function (OrganizationUnit $ou,$key) use ($script,$currentUserPendingStatus,$pendingStatus) {
            $status = $key == 0 ? $currentUserPendingStatus : $pendingStatus;
            return [
                'assignedUserID' => $ou->head->id,
                'priority' => $key + 1,
                'scriptID' => $script->id,
                'statusID' => $status->id,

            ];
        });

        return $result->toArray();
}
}
