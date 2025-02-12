<?php

namespace Modules\ACC\app\Http\Enums;

use Modules\ACC\app\Models\DetailAccount;
use Modules\ACC\app\Models\GlAccount;
use Modules\ACC\app\Models\SubAccount;

enum AccountLayerTypesEnum: string
{
    case MAIN = GlAccount::class;
    case SUB = SubAccount::class;
    case DETAIL = DetailAccount::class;

    public static function getChildTypeOfCurrentParent(?self $parentType): ?self
    {
        return match ($parentType) {
            null => self::MAIN,
            self::MAIN => self::SUB,
            self::SUB, self::DETAIL => self::DETAIL,
        };
    }

    public function getLabel()
    {
        return match ($this) {
            self::MAIN => 'حساب کل',
            self::SUB => 'حساب معین',
            self::DETAIL => 'حساب تفضیلی',
        };

    }

    public static function getLayerByID(int $id): ?string
    {
        $layer = match ($id) {
            1 => self::MAIN,
            2 => self::SUB,
            3 => self::DETAIL,
            default => null,
        };
        return $layer?->value ?? null;
    }


}
