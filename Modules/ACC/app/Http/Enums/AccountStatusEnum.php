<?php

namespace Modules\ACC\app\Http\Enums;

enum AccountStatusEnum: string
{
    case ACTIVE = 'فعال';
    case INACTIVE = 'غیرفعال';
    case IMPORTED = 'ایمپورت شده';
}
