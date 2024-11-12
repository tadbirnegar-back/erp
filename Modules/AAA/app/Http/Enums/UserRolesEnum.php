<?php

namespace Modules\AAA\app\Http\Enums;

enum UserRolesEnum: string
{
    case BAKHSHDAR = 'بخشدار';
    case KARSHENAS = 'کارشناس مشورتی';
    case DABIRKHANE = 'مسئول دبیرخانه';
    case OZV_HEYAAT = 'عضو هیئت';
}
