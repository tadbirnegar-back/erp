<?php

namespace Modules\BDM\app\Http\Enums;

enum PlaceTypesEnum: string
{
    case CENTER = 'مرکز';

    public function id(): int
    {
        return match ($this) {
            self::CENTER => 1,
        };
    }

    public static function listWithIds(): array
    {
        return array_map(fn($case) => [
            'id' => $case->id(),
            'name' => $case->value,
        ], self::cases());
    }

    public static function getNameById(int $id): string
    {
        return array_values(self::listWithIds())[$id - 1]['name'];
    }
}
