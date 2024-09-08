<?php

namespace Modules\EMS\app\Http\Enums;

enum EnactmentReviewEnum: string
{
    case INCONSISTENCY = 'مغایرت';
    case NO_INCONSISTENCY = 'عدم مغایرت';
    case SYSTEM_NO_INCONSISTENCY = 'عدم مغایرت سیستمی';
    case UNKNOWN = 'نامشخص';
}
