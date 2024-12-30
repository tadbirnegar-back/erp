<?php

namespace Modules\ACMS\app\Http\Enums;

enum CircularStatusEnum: string
{
    case DRAFT = 'پیش نویس';
    case APPROVED = 'ابلاغ شده';
    case REJECTED = 'رد شده';
}
