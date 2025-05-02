<?php

namespace Modules\BDM\app\Http\Enums;

enum GeographicalCordinatesTypesEnum: string
{
    case SUBMITTED = 'ارسال شده';

    public function id(): int
    {
        return match ($this) {
            self::SUBMITTED => 1,

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
