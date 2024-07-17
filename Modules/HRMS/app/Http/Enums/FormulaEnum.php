<?php

namespace Modules\HRMS\app\Http\Enums;

enum FormulaEnum: int
{
case Formula1 = 1;
case Formula2 = 2;

    public function getLabel()
    {
        return match ($this) {
            self::Formula1 => 'فرمول یک',
            self::Formula2 => 'فرمول دو',
        };
    }
    public function getLabelAndValue()
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->value
        ];
    }
    public static function formulaList()
    {
        $cats = collect(self::cases());

        $result = $cats->map(fn($item, $key) => [
            'value' => $item->value,
            'label' => $item->getLabel(),
        ]);

        return $result;
    }
}
