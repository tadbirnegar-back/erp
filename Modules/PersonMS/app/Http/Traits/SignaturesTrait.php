<?php

namespace Modules\PersonMS\app\Http\Traits;

use Modules\HRMS\app\Models\ExemptionType;
use Modules\HRMS\app\Models\MilitaryService;
use Modules\PersonMS\app\Http\Enums\PersonLicensesEnums;
use Modules\PersonMS\app\Http\Enums\SignatureStatusesEnum;
use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\PersonMS\app\Models\PersonLicense;
use Modules\PersonMS\app\Models\Signature;

trait SignaturesTrait
{
    public function activeSignatureStatus()
    {
        return Signature::GetAllStatuses()->firstWhere('name', '=', SignatureStatusesEnum::ACTIVE->value);
    }

    public function notActiveSignatureStatus()
    {
        return Signature::GetAllStatuses()->firstWhere('name', '=', SignatureStatusesEnum::INACTIVE->value);
    }
}
