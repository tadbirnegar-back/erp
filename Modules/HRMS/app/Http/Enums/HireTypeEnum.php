<?php

namespace Modules\HRMS\app\Http\Enums;

use Modules\OUnitMS\app\Models\OrganizationUnit;

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

    public static function getHireTypeByOunit(OrganizationUnit $ounit)
    {
        $village = $ounit->village;
        if ($village) {
            $population = $village->population_1395;
            $hireType = match ($population) {
                $population < 100 => self::PART_TIME,
                $population >= 100 && $population <= 200 => self::PART_TIME,
                $population > 200 && $population <= 400 => self::PART_TIME,
                $population > 400 && $population <= 600 => self::PART_TIME,
                $population > 600 && $population <= 800 => self::PART_TIME,
                $population > 800 && $population <= 999 => self::PART_TIME,
                default => self::FULL_TIME,

            };
        } else {
            $hireType = self::FULL_TIME;
        }

        return $hireType;
    }
}
