<?php

namespace Modules\BDM\app\Http\Enums;

enum BdmOwnershipTypesEnum: string
{
    case SHISHDANG = 'شش دانگ';
    case MOSHAIE = 'مشاعی';
    case VAGOZARI = "برگه واگذاری";

    case GHOLNAME = 'قولنامه';
    case OTHERS = 'سایر';

    public function id(): int
    {
        return match ($this) {
            self::SHISHDANG => 1,
            self::MOSHAIE => 2,
            self::VAGOZARI => 3,
            self::GHOLNAME => 4,
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

    public static function getNameById(int $id): string
    {
        return array_values(self::listWithIds())[$id - 1]['name'];
    }
}
