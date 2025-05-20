<?php

namespace Modules\HRMS\app\Http\Enums;

enum EducationalRecordStatusEnum: string
{
    case PENDING_APPROVE = 'در انتظار تایید';
    case APPROVED = 'تایید شده';
    case PENDING_TO_FILL = 'در انتظار تکمیل';
}
