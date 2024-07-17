<?php

namespace Modules\HRMS\app\Http\Enums;

enum OunitCategoryEnum: int
{
    case StateOfc = 1;
    case CityOfc = 2;
    case DistrictOfc = 3;
    case TownOfc = 4;
    case VillageOfc = 5;


    public function getLabel()
    {
        return match ($this) {
            self::StateOfc => 'استانداری',
            self::CityOfc => 'فرمانداری',
            self::DistrictOfc => 'بخشداری',
            self::TownOfc => 'دهستان',
            self::VillageOfc => 'دهیاری',
        };
    }

public function getLabelAndValue()
{
    return [
        'label' => $this->getLabel(),
        'value' => $this->value
    ];
}
    public static function ounitCatList()
    {
        $cats = collect(self::cases());

        $result = $cats->map(fn($item, $key) => [
            'value' => $item->value,
            'label' => $item->getLabel(),
        ]);

        return $result;
    }
}
