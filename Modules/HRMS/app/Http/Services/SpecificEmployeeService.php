<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;

class SpecificEmployeeService
{
    use ApprovingListTrait;
    public static function procedureIdentifier(int $optionID = null)
    {
        $employee = Employee::with('person')->find($optionID);
        return [
            'display_name' => $employee->person->display_name,
            'employee_id' => $employee->id,
        ];
    }

    public static function generateApprovers(?int $optionID,RecruitmentScript $script)
    {
        $employee = Employee::with('person.user');

        $result[0]['assignedUserID'] = $employee->person->user->id;
        $result[0]['priority'] = '1';
        $result[0]['statusID'] = self::pendingForCurrentUserStatus()->id;
        $result[0]['scriptID'] = $script->id;

        return $result;
    }
}
