<?php

namespace Modules\PersonMS\app\Http\Enums;

enum PersonLicensesEnums: string
{
    case BIRTH_CERTIFICATE = "شناسنامه";
    case NATIONAL_ID_CARD = "کارت ملی";

    public function id(): int
    {
        return match ($this) {
            self::BIRTH_CERTIFICATE => 1,
            self::NATIONAL_ID_CARD => 2,
        };
    }

}
