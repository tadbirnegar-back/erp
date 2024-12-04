<?php

namespace Modules\OUnitMS\app\Http\Enums;

enum statusEnum: string
{
    case Active = "فعال";
    case Inactive = "غیرفعال";


    public function getLabel(): string
    {
        return match ($this) {
            self::Active => 'فعال',
            self::Inactive => 'غیرفعال',

        };
    }
}
