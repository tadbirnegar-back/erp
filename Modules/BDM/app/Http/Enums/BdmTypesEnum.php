<?php

namespace Modules\BDM\app\Http\Enums;

enum BdmTypesEnum: string
{
    case OSHKUB = 'اضافه اشکوب';
    case REMAKE_AND_DESTRUCTION = 'نوسازی و تخریب';
    case REMAKE = 'نوسازی';

    public function id(): int
    {
        return match ($this) {
            self::OSHKUB => 1,
            self::REMAKE_AND_DESTRUCTION => 2,
            self::REMAKE => 3,
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
