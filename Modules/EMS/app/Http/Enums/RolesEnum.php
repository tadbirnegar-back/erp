<?php

namespace Modules\EMS\app\Http\Enums;

enum RolesEnum: string
{
    case BAKHSHDAR = 'بخشدار';
    case KARSHENAS_OSTANDARI = 'کارشناس استانداری';
    case DABIR_HEYAAT = 'دبیر هیئت';
    case KARSHENAS_MASHVARATI = 'کارشناس مشورتی';
    case OZV_HEYAAT = 'عضو هیئت';
    case OZV_SHOURA_RUSTA = 'عضو شورای روستا';
}

