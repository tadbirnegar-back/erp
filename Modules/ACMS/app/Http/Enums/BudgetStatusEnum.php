<?php

namespace Modules\ACMS\app\Http\Enums;

enum BudgetStatusEnum: string
{
    case PROPOSED = 'در انتظار پیشنهاد بودجه';
    case PENDING_FOR_APPROVAL = 'در انتظار تایید شورا';
    case PENDING_FOR_HEYAAT_APPROVAL = 'در انتظار تایید هیئت';
    case FINALIZED = 'تصویب شده';
    case CANCELED = 'رد شده';

}
