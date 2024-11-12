<?php

namespace Modules\HRMS\app\Http\Enums;

enum RecruitmentStatusEnum: string
{
    case FAAL = 'فعال';
    case GHEYREFAAL = 'غیرفعال';
    case DARENTEZARETAIED = 'در انتظار تایید';

}
