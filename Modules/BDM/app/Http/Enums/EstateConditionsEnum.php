<?php

namespace Modules\BDM\app\Http\Enums;

enum EstateConditionsEnum : string
{
    case WITHOUT_BUILDING = 'فاقد بنا';
    case NEW_BUILDING = 'ساختمان نوساز';
    case HALF_DONE_BUILDING = 'ساختمان نیمه تمام';
    case DESTROYED_BUILDING = 'ساختمان تخریبی';
    case OTHERS = 'سایر';

    public function id(): int
    {
        return match ($this) {
            self::WITHOUT_BUILDING => 1,
            self::NEW_BUILDING => 2,
            self::HALF_DONE_BUILDING => 3,
            self::DESTROYED_BUILDING => 4,
            self::OTHERS => 5,
        };
    }

    public static function listWithIds(): array
    {
        return array_map(fn($case) => [
            'id' => $case->id(),
            'name' => $case->value,
        ], self::cases());
    }
}
