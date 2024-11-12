<?php

namespace Modules\HRMS\app\Http\Enums;

enum ScriptTypeOriginEnum: int
{
    case Main = 1;
    case Sub = 2;


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
            self::Main => 'اصلی',
            self::Sub => 'الحاقی',
        };
    }

    public static function getAllLabelsAndValues()
    {
        return collect(self::cases())->map(function ($item) {
            return $item->getLabelAndValue();
        })->toArray();

    }

}
