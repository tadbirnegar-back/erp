<?php

namespace Modules\PayStream\App\Http\Enums;

enum PsPaymentStatusEnum: string
{
    case SUCCESS = "پرداخت شده";
    case FAILED = "لغو";
    case WAITING = "در انتظار پرداخت";

}
