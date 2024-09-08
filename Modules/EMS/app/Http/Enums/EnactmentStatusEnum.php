<?php

namespace Modules\EMS\app\Http\Enums;

enum EnactmentStatusEnum: string
{
    case COMPLETED = 'تکمیل شده';
    case PENDING_BOARD_REVIEW = 'در انتظار بررسی هیئت';
    case PENDING_SECRETARY_REVIEW = 'در انتظار وصول';
    case CANCELED = 'لغو شده';
}
