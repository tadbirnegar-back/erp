<?php

namespace Modules\HRMS\app\Http\Enums;

enum HireTypeEnum: string
{
    case PART_TIME = 'پاره وقت';
    case FULL_TIME = 'تمام وقت';

    public function getCalculateClassPrefix()
    {
        return match ($this) {
            self::PART_TIME => 'PartTime',
            self::FULL_TIME => 'FullTime',
        };
    }
}
