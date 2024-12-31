<?php

namespace Modules\ACMS\app\Http\Enums;

enum BudgetStatusEnum: string
{
    case PROPOSED = 'پیشنهاد شده';
    case FINALIZED = 'نهایی شده';
    case CANCELED = 'حذف شده';

}
