<?php

namespace Modules\EMS\app\Http\Enums;

enum MeetingStatusEnum: string
{
    case DRAFT = 'پیش نویس';
    case APPROVED = 'تایید شده';
    case MEETING_COMPLETED = 'اتمام جلسه';
    case CANCELED = 'لغو شده';
    case HELD = 'برگزار شده';
}
