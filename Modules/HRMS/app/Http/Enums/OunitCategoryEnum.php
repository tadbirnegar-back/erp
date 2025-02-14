<?php

namespace Modules\HRMS\app\Http\Enums;

use Modules\OUnitMS\app\Models\{CityOfc, Department, DistrictOfc, FreeZone, StateOfc, TownOfc, VillageOfc};

enum OunitCategoryEnum: int
{
    case StateOfc = 1;
    case CityOfc = 2;
    case DistrictOfc = 3;
    case TownOfc = 4;
    case VillageOfc = 5;
    case Department = 6;
    case FreeZone = 7;


    public function getLabel()
    {
        return match ($this) {
            self::StateOfc => 'استانداری',
            self::CityOfc => 'فرمانداری',
            self::DistrictOfc => 'بخشداری',
            self::TownOfc => 'دهستان',
            self::VillageOfc => 'دهیاری',
            self::Department => 'دپارتمان',
            self::FreeZone => 'منطقه آزاد'
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
            self::Department => Department::class,
            self::FreeZone => FreeZone::class,
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

    public static function getDesiredLabelWithValue($label)
    {
        foreach (self::cases() as $case) {
            if ($case->getUnitableType() === $label) {
                return $case->value;
            }
        }
        throw new \InvalidArgumentException("label not found: $label");

    }

    public static function getValueFromlabel($label)
    {
        foreach (self::cases() as $case) {
            if ($case->getUnitableType() == $label) {
                return $case->value;
            }
        }
        throw new \InvalidArgumentException("label not found: $label");

    }

    public static function getLabelById(int $id)
    {
        foreach (self::cases() as $case) {
            if ($case->value === $id) {
                return $case->getLabel();
            }
        }

        // Optionally, handle cases where the ID is not found
        throw new \InvalidArgumentException("ID not found: $id");
    }

    public static function getModelByValue(int $value)
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case->getUnitableType();
            }
        }

        // Optionally, handle cases where the value is not found
        throw new \InvalidArgumentException("Value not found: $value");
    }

    public static function getModelsByValues(array $values)
    {
        $models = [];

        foreach ($values as $value) {
            foreach (self::cases() as $case) {
                if ($case->value === $value) {
                    $models[] = $case->getUnitableType();
                    break;
                }
            }
        }

        return $models;
    }


}
