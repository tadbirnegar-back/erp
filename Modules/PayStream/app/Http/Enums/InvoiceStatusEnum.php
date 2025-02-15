<?php

namespace Modules\PayStream\app\Http\Enums;

enum InvoiceStatusEnum: string
{
    case DECLINED = "لغو";
    case WAITING = "در انتظار پرداخت";
    case PAYED = "پرداخت شده";

}
