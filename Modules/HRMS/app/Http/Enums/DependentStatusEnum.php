<?php

namespace Modules\HRMS\app\Http\Enums;

enum DependentStatusEnum: string
{
    case ACTIVE = 'تایید شده';
    case PENDING = 'در انتظار تایید';
    case REJECTED = 'رد شده';
}
