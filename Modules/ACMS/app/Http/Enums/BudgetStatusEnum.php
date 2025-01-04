<?php

namespace Modules\ACMS\app\Http\Enums;

enum BudgetStatusEnum: string
{
    case PROPOSED = 'در انتظار پیشنهاد بودجه';
    case PENDING_FOR_APPROVAL = 'در انتظار تصویب بودجه';
    case FINALIZED = 'تصویب شده';
    case CANCELED = 'حذف شده';

}
