<?php

namespace Modules\HRMS\app\Http\Enums;

enum EducationalRecordStatusEnum: string
{
    case PENDING_APPROVE = 'pending_approve';
    case APPROVED = 'approved';
}
