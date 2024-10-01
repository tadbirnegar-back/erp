<?php

namespace Modules\HRMS\app\Http\Enums;

use Modules\OUnitMS\app\Models\{CityOfc, DistrictOfc, StateOfc, TownOfc, VillageOfc};

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

    public function getUnitableType()
    {
        return match ($this) {
            self::StateOfc => StateOfc::class,
            self::CityOfc => CityOfc::class,
            self::DistrictOfc => DistrictOfc::class,
            self::TownOfc => TownOfc::class,
            self::VillageOfc => VillageOfc::class,
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
