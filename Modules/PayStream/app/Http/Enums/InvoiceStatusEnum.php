<?php

namespace Modules\PayStream\App\Http\Enums;

enum InvoiceStatusEnum: string
{
    case DECLINED = "لغو";
    case WAITING = "در انتظار پرداخت";
    case PAYED = "پرداخت شده";

}
