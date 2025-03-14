<?php

namespace Modules\BNK\app\Http\Enums;

enum ChequeStatusEnum: string
{
    case BLANK = 'سفید';
    case CLEARED = 'پاس شده';
    case CANCELED = 'ابطال شده';
    case BOUNCED = 'برگشت خورده';
    case ISSUE = 'صادر شده';
    case DELETED = 'حذف شده';

}
