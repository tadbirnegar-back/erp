<?php

namespace Modules\ACC\app\Http\Enums;

enum AccountCategoryTypeEnum: int
{
    case BALANCE_SHEET = 1;
    case BUDGETARY = 2;
    case REGULATORY = 3;

    public function getLabel()
    {
        return match ($this) {
            self::BALANCE_SHEET => 'ترازنامه ای',
            self::BUDGETARY => 'بودجه ای',
            self::REGULATORY => 'انتظامی',
        };

    }

    public function getLabelAndValue()
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->value,
        ];

    }

    public static function getAllLabelAndValues()
    {
        return collect(self::cases())->map(function ($item) {
            return $item->getLabelAndValue();
        });

    }
}
