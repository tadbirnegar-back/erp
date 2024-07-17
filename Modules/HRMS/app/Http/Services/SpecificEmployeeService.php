<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Models\Employee;

class SpecificEmployeeService
{
public static function procedureIdentifier(int $optionID=null)
{
    $employee = Employee::with('person')->find($optionID);
    return [
        'display_name' => $employee->person->display_name,
        'employee_id' => $employee->id,
    ];
}
}
