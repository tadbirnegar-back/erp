<?php

namespace Modules\PFM\app\Http\Enums;

enum BookletStatusEnum: string
{
    case MOSAVAB = 'تصویب شده';
    case DAR_ENTEZAR_SABTE_MAGHADIR = 'در انتظار ثبت مقادیر';
    case RAD_SHODE = 'رد شده';
    case DAR_ENTEZAR_SHURA = 'در انتظار تایید شورا روستا';
    case DAR_ENTEZARE_HEYATE_TATBIGH = 'در انتظار هیئت تطبیق';



}
