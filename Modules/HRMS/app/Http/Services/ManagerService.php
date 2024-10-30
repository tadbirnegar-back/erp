<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Models\RecruitmentScript;

class ManagerService
{

    use ApprovingListTrait;

    public static function procedureIdentifier(int $optionID = null)
    {
        return null;
    }

    public static function generateApprovers(?int $optionID, RecruitmentScript $script)
    {
        $ou = $script->load('organizationUnit.head');

        $result[0]['assignedUserID'] = $ou->organizationUnit->head?->id;
        $result[0]['scriptID'] = $script->id;

        return $result;
    }
}

