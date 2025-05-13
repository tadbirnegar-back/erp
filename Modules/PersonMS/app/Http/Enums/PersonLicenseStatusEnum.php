<?php

namespace Modules\PersonMS\app\Http\Enums;

enum PersonLicenseStatusEnum: string
{
    case PENDING = 'در انتظار تایید';
    case APPROVED = 'تایید شد';
    case REJECTED = 'رد شد';
}
