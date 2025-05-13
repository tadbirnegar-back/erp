<?php

namespace Modules\PayStream\app\Http\Enums;

enum OrderStatusEnum: string
{
    case PROC_WAITE_MALI = "در انتظار مالی";
    case PROC_REGISTERED = "ثبت نام شده";
    case PROC_CANCELED = "لغو شده";
    case PROC_EXPIRED = "منقضی شده";
    case PROC_PAYED = "تسویه عوارض";

    case FIN_WAIT_PARDAKHT = "در انتظار پرداخت";

    case  FIN_PARDAKHT_SHODE = "پرداخت شده";

    case FIN_CANCELED = "رد شده";
}
