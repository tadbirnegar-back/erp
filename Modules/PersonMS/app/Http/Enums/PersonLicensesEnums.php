<?php

namespace Modules\PersonMS\app\Http\Enums;

enum PersonLicensesEnums: int
{
    case BIRTH_CERTIFICATE = 1;
    case NATIONAL_ID_CARD = 2;

    public function name(): string
    {
        return match ($this) {
            self::BIRTH_CERTIFICATE => 'شناسنامه',
            self::NATIONAL_ID_CARD => 'رو کارت ملی',
        };
    }

}
