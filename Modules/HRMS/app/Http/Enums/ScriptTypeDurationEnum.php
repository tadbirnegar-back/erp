<?php

namespace Modules\HRMS\app\Http\Enums;

enum ScriptTypeDurationEnum: int
{
    case THREE = 3;
    case SIX = 6;
    case NINE = 9;
    case TWELVE = 12;
    case EIGHTEEN = 18;
    case TWENTY_FOUR = 24;
    case THIRTY_SIX = 36;
    case FORTY_EIGHT = 48;
    case INFINITE = 0;


    public function getLabelAndValue()
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->value
        ];
    }

    public function getLabel()
    {
        return match ($this) {
            self::THREE => '3 ماه',
            self::SIX => '6 ماه',
            self::NINE => '9 ماه',
            self::TWELVE => '1 سال',
            self::EIGHTEEN => '18 ماه',
            self::TWENTY_FOUR => '2 سال',
            self::THIRTY_SIX => '3 سال',
            self::FORTY_EIGHT => '4 سال',
            self::INFINITE => 'نامحدود',
        };
    }

    public static function getAllLabelsAndValues()
    {
        return collect(self::cases())->map(function ($item) {
            return $item->getLabelAndValue();
        })->toArray();

    }
}
