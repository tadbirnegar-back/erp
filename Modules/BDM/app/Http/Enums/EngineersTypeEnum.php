<?php

namespace Modules\BDM\app\Http\Enums;

enum EngineersTypeEnum : string
{
    case NAZER = 'نظارت';
    case MEMAR = 'معماری';
    case MOHASEB = 'محاسبات سازه';

    public function id(): int
    {
        return match ($this) {
            self::NAZER => 1,
            self::MEMAR => 2,
            self::MOHASEB => 3,
        };
    }

    public static function listWithIds(): array
    {
        return array_map(fn($case) => [
            'id' => $case->id(),
            'name' => $case->value,
        ], self::cases());
    }

    public static function getNameById(int $id): ?string
    {
        foreach (self::cases() as $case) {
            if ($case->id() === $id) {
                return $case->value;
            }
        }

        return null;
    }
}
