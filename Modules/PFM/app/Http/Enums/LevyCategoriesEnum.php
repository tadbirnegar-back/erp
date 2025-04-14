<?php

namespace Modules\PFM\app\Http\Enums;

enum LevyCategoriesEnum: string
{
    case ARAZI = 'اراضی';

    case MOSTAHADESAT = 'مستحدثات';

    case TASISAT = 'تأسیسات';

    case TABLIGHAT = 'تبلیغات';

    case ARZESH_AFZOUDE = 'ارزش افزوده ناشی از اجرای طرح های توسعه روستایی';

    case BAHAYE_KHEDMAT = 'بهای خدمت';

    public function id(): int
    {
        // Never change these values
        return match($this) {
            self::ARAZI => 1,
            self::MOSTAHADESAT => 2,
            self::TASISAT => 3,
            self::TABLIGHAT => 4,
            self::ARZESH_AFZOUDE => 5,
            self::BAHAYE_KHEDMAT => 6,
        };
    }

    public static function fromId(int $id): ?self
    {
        // Never change these values
        return match($id) {
            1 => self::ARAZI,
            2 => self::MOSTAHADESAT,
            3 => self::TASISAT,
            4 => self::TABLIGHAT,
            5 => self::ARZESH_AFZOUDE,
            6 => self::BAHAYE_KHEDMAT,
            default => null,
        };
    }

    public static function getLevyCategoryIdByName(string $name): ?int
    {
        foreach (self::cases() as $case) {
            if ($case->value === $name) {
                return $case->id();
            }
        }

        return null;
    }

}
