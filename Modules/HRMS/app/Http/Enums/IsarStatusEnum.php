<?php

namespace Modules\HRMS\app\Http\Enums;

enum IsarStatusEnum: string
{
    case PENDING_APPROVE = 'در انتظار تایید';
    case APPROVED = 'تایید شده';
    case REJECTED = 'رد شده';
}
