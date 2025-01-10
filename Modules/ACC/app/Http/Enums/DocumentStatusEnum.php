<?php

namespace Modules\ACC\app\Http\Enums;

enum DocumentStatusEnum: string
{
    case DRAFT = 'پیش نویس';
    case CONFIRMED = 'قطعی';
    case CANCELED = 'لغو شده';
}
