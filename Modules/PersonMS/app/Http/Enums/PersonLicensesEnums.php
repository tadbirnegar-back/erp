<?php

namespace Modules\PersonMS\app\Http\Enums;

enum PersonLicensesEnums: int
{
    case BIRTH_CERTIFICATE = 1;
    case MARRIAGE_PAGE = 3;
    case NATIONAL_ID_CARD = 2;
    case BACK_OF_ID_CARD = 4;
    case CHILDREN_PAGE = 5;

    public function name(): string
    {
        return match ($this) {
            self::BIRTH_CERTIFICATE => 'صفحه اول شناسنامه',
            self::MARRIAGE_PAGE => 'صفحه ازدواج شناسنامه',
            self::CHILDREN_PAGE => 'صفحه اطلاعات فرزندان',
            self::NATIONAL_ID_CARD => 'رو کارت ملی',
            self::BACK_OF_ID_CARD => 'پشت کارت ملی',
        };
    }

}
