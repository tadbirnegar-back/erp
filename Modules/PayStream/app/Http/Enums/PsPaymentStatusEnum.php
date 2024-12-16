<?php

namespace Modules\PayStream\App\Http\Enums;

enum PsPaymentStatusEnum: string
{
    case SUCCESS = "موفق";
    case FAILED = "ناموفق";
    case WAITING = "در انتظار پرداخت";

}
