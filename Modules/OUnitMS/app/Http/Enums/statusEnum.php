<?php

namespace Modules\OUnitMS\app\Http\Enums;

enum statusEnum: int
{
    case Active = 1;
    case Inactive = 2;


    public function getLabel(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',

        };
    }
}
