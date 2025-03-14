<?php

namespace Modules\BNK\app\Http\Enums;

enum CardStatusEnum: string
{
    case CANCELED = 'ابطال شده';
    case ACTIVE = 'فعال';
    case LOST = 'گم شده';
    case EXPIRED = 'منقضی شده';
}
