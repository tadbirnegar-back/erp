<?php

namespace Modules\BNK\app\Http\Enums;

enum ChequeBookStatusEnum: string
{
    case ACTIVE = 'فعال';
    case CANCELED = 'غیرفعال';
    case LOST = 'گم شده';
    case EXHAUSTED = 'تمام شده';
    case EXPIRED = 'منقضی شده';
}
