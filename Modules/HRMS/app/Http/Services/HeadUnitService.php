<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\OUnitMS\app\Models\OrganizationUnit;

class HeadUnitService
{
    public static function procedureIdentifier(int $optionID = null)
    {
        $ounit = OrganizationUnit::with('person')->find($optionID);
        return $ounit;
    }
}
