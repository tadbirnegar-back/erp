<?php

namespace Modules\BDM\app\Http\Enums;

enum FieldConditionsEnum : string
{
    case HAVE_TREE_FIELD = 'زمین مشجر';
    case DOES_NOT_HAVE_TREE_FIELD = 'زمین غیر مشجر';

    public function id(): int
    {
        return match ($this) {
            self::HAVE_TREE_FIELD => 1,
            self::DOES_NOT_HAVE_TREE_FIELD => 2,
        };
    }

    public static function getNameById(int $id): string
    {
        return array_values(self::listWithIds())[$id - 1]['name'];
    }
    public static function listWithIds(): array
    {
        return array_map(fn($case) => [
            'id' => $case->id(),
            'name' => $case->value,
        ], self::cases());
    }
}
