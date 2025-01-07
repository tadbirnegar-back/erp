<?php

namespace Modules\ACC\app\Http\Enums;

enum AccCategoryEnum: int
{
    case CURRENT_ASSETS = 1;
    case CURRENT_LIABILITIES = 3;
    case SURPLUS_DEFICIT = 5;
    case INCOME = 6;
    case EXPENSE = 7;
    case REGULATORY_ACCOUNTS = 8;

    public function getLabel()
    {
        return match ($this) {
            self::CURRENT_ASSETS => 'دارائیهای جاری',
            self::CURRENT_LIABILITIES => 'بدهیهای جاری',
            self::SURPLUS_DEFICIT => 'مازاد و کسری',
            self::INCOME => 'درآمد',
            self::EXPENSE => 'هزینه',
            self::REGULATORY_ACCOUNTS => 'حسابهای انتظامی',
        };
    }

    public function getCatTypeEnum()
    {
        return match ($this) {
            self::CURRENT_ASSETS, self::SURPLUS_DEFICIT, self::CURRENT_LIABILITIES => AccountCategoryTypeEnum::BALANCE_SHEET,

            self::INCOME, self::EXPENSE => AccountCategoryTypeEnum::BUDGETARY,

            self::REGULATORY_ACCOUNTS => AccountCategoryTypeEnum::REGULATORY,
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
