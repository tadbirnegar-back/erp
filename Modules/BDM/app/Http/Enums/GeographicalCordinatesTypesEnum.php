<?php

namespace Modules\BDM\app\Http\Enums;

enum GeographicalCordinatesTypesEnum: string
{
    case SUBMITTED = 'ارسال شده';
    case ARZ_GOZAR = 'عرض موجود گذر';
    case ABADE_MELK_PAS_AZ_ESLAH = 'ابعاد ملک پس از اصلاح';
    case ARZE_MOSAVABE_GOZAR = 'عرض مصوب گذر';
    case VAZIYATE_HUDUDE_HAMSAYEGI = 'وضعیت حدود همسایگی';




    public function id(): int
    {
        return match ($this) {
            self::SUBMITTED => 1,
            self::ARZ_GOZAR => 2,
            self::ABADE_MELK_PAS_AZ_ESLAH => 3,
            self::ARZE_MOSAVABE_GOZAR => 4,
            self::VAZIYATE_HUDUDE_HAMSAYEGI => 5,

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
