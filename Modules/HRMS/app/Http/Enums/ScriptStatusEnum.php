<?php

namespace Modules\HRMS\app\Http\Enums;

enum ScriptStatusEnum: string
{
    case PAYANKHEDMAT = 'پایان خدمت';
    case EBTAL = 'ابطال';

    case FAAL = 'فعال';

    case GHEYREFAAL = 'غیر فعال';
}
