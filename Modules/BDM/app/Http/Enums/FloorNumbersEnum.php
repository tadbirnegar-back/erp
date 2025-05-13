<?php

namespace Modules\BDM\app\Http\Enums;

enum FloorNumbersEnum : string
{
    case FIRST_FLOOR = 'طبقه اول';
    case SECOND_FLOOR = 'طبقه دوم';
    case THIRD_FLOOR = 'طبقه سوم';
    case FOURTH_FLOOR = 'طبقه چهارم';
    case FIFTH_FLOOR = 'طبقه پنجم';
    case SIXTH_FLOOR = 'طبقه ششم';
    case SEVENTH_FLOOR = 'طبقه هفتم';
    case EIGHTH_FLOOR = 'طبقه هشتم';
    case NINTH_FLOOR = 'طبقه نهم';
    case TENTH_FLOOR = 'طبقه دهم';
    case ELEVENTH_FLOOR = 'طبقه یازدهم';
    case TWELFTH_FLOOR = 'طبقه دوازدهم';
    case THIRTEENTH_FLOOR = 'طبقه سیزدهم';
    case FOURTEENTH_FLOOR = 'طبقه چهاردهم';
    case FIFTEENTH_FLOOR = 'طبقه پانزدهم';
    case SIXTEENTH_FLOOR = 'طبقه شانزدهم';
    case SEVENTEENTH_FLOOR = 'طبقه هفدهم';
    case EIGHTEENTH_FLOOR = 'طبقه هجدهم';
    case NINETEENTH_FLOOR = 'طبقه نوزدهم';
    case TWENTIETH_FLOOR = 'طبقه بیستم';
    case TWENTYFIRST_FLOOR = 'طبقه بیست و یکم';

    case TWENTY_SECOND_FLOOR = 'طبقه بیست و دوم';
    case TWENTY_THIRD_FLOOR = 'طبقه بیست و سوم';
    case TWENTY_FOURTH_FLOOR = 'طبقه بیست و چهارم';
    case TWENTY_FIFTH_FLOOR = 'طبقه بیست و پنجم';
    case TWENTY_SIXTH_FLOOR = 'طبقه بیست و ششم';
    case TWENTY_SEVENTH_FLOOR = 'طبقه بیست و هفتم';
    case TWENTY_EIGHTH_FLOOR = 'طبقه بیست و هشتم';
    case TWENTY_NINTH_FLOOR = 'طبقه بیست و نهم';
    case THIRTY_FLOOR = 'طبقه سی ام';

    public function id(): int
    {
        return match ($this) {
            self::FIRST_FLOOR => 1,
            self::SECOND_FLOOR => 2,
            self::THIRD_FLOOR => 3,
            self::FOURTH_FLOOR => 4,
            self::FIFTH_FLOOR => 5,
            self::SIXTH_FLOOR => 6,
            self::SEVENTH_FLOOR => 7,
            self::EIGHTH_FLOOR => 8,
            self::NINTH_FLOOR => 9,
            self::TENTH_FLOOR => 10,
            self::ELEVENTH_FLOOR => 11,
            self::TWELFTH_FLOOR => 12,
            self::THIRTEENTH_FLOOR => 13,
            self::FOURTEENTH_FLOOR => 14,
            self::FIFTEENTH_FLOOR => 15,
            self::SIXTEENTH_FLOOR => 16,
            self::SEVENTEENTH_FLOOR => 17,
            self::EIGHTEENTH_FLOOR => 18,
            self::NINETEENTH_FLOOR => 19,
            self::TWENTIETH_FLOOR => 20,
            self::TWENTYFIRST_FLOOR => 21,
            self::TWENTY_SECOND_FLOOR => 22,
            self::TWENTY_THIRD_FLOOR => 23,
            self::TWENTY_FOURTH_FLOOR => 24,
            self::TWENTY_FIFTH_FLOOR => 25,
            self::TWENTY_SIXTH_FLOOR => 26,
            self::TWENTY_SEVENTH_FLOOR => 27,
            self::TWENTY_EIGHTH_FLOOR => 28,
            self::TWENTY_NINTH_FLOOR => 29,
            self::THIRTY_FLOOR => 30,
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
