<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\OUnitMS\app\Models\OrganizationUnit;

class HeadUnitService
{
    use ApprovingListTrait;

    public static function procedureIdentifier(int $optionID = null)
    {
        $ounit = OrganizationUnit::with('person')->find($optionID);
        return $ounit;
    }

    public static function generateApprovers(?int $optionID , RecruitmentScript $script)
    {
        $ounit = OrganizationUnit::with('head')
            ->find($optionID);

        $result[0]['assignedUserID'] = $ounit->head->id;
        $result[0]['priority'] = '1';
        $result[0]['statusID'] = self::pendingForCurrentUserStatus()->id;
        $result[0]['scriptID'] = $script->id;

        return $result;
    }


}
