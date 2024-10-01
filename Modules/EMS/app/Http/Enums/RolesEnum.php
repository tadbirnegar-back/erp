<?php

namespace Modules\EMS\app\Http\Enums;

enum RolesEnum: string
{
    case BAKHSHDAR = 'بخشدار';
    case KARSHENAS_OSTANDARI = 'مسئول استانداری کل';
    case DABIR_HEYAAT = 'مسئول دبیرخانه';
    case KARSHENAS_MASHVARATI = 'کارشناس مشورتی';
    case OZV_HEYAAT = 'عضو هیئت';
    case OZV_SHOURA_RUSTA = 'شورای روستا';
}

