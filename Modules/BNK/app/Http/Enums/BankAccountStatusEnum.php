<?php

namespace Modules\BNK\app\Http\Enums;

enum BankAccountStatusEnum: string
{
    case ACTIVE = 'فعال';
    case INACTIVE = 'بسته شده';
}
