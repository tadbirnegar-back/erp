<?php

namespace Modules\HRMS\app\Http\Enums;

enum RecruitmentScriptStatusEnum: string
{
    case ACTIVE = 'فعال';
    case INACTIVE = 'غیرفعال';
    case EXPIRED = 'منقضی شده';
    case PENDING_APPROVAL = 'در انتظار تایید';
    case REJECTED = 'رد شده';
    case TERMINATED = 'عزل شده';
    case SERVICE_ENDED = 'پایان خدمت';
    case CANCELED = 'باطل شده';
    case PENDING_FOR_TERMINATE = 'در انتظار عزل';
}
