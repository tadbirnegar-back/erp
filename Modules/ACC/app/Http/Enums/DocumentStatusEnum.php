<?php

namespace Modules\ACC\app\Http\Enums;

enum DocumentStatusEnum: string
{
    case DRAFT = 'پیش نویس';
    case CONFIRMED = 'قطعی';
    case DELETED = 'حذف شده';
}
