<?php

namespace Modules\ACC\app\Http\Enums;

enum TransactionStatusEnum: string
{
    case ACTIVE = 'فعال';
    case DELETED = 'حذف شده';
    case SYNCED = 'سینک شده';

}
