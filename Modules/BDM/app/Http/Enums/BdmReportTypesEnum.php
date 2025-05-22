<?php


namespace Modules\BDM\app\Http\Enums;

enum BdmReportTypesEnum: int
{
    case FIRST_REPORT = 1;
    case SECOND_REPORT = 2;
    case THIRD_REPORT = 3;
    case FOURTH_REPORT = 4;

    public function getName(): string
    {
        return match ($this) {
            self::FIRST_REPORT => 'گزارش بتن ریزی پی',
            self::SECOND_REPORT => 'اجرای اسکله و عملیات سازه‌ها',
            self::THIRD_REPORT => 'اجرای عملیات سفت کاری و نازک کاری',
            self::FOURTH_REPORT => 'گزارش نهایی کار',
        };
    }
}
