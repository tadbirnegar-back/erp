<?php

namespace Modules\PayStream\App\Http\Enums;

enum OrderStatusEnum: string
{
    case PROC_WAITE_MALI = "در انتظار مالی";
    case PROC_REGISTERED = "ثبت نام شده";
    case PROC_CANCELED = "لغو شده";


    case FIN_WAIT_PARDAKHT = "در انتظار پرداخت";

    case  FIN_PARDAKHT_SHODE = "پرداخت شده";

    case FIN_CANCELED = "رد شده";
}
