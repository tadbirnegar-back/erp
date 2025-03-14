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

    public function getAccCategories()
    {
        return match ($this) {
            self::BALANCE_SHEET => [AccCategoryEnum::CURRENT_ASSETS, AccCategoryEnum::SURPLUS_DEFICIT, AccCategoryEnum::CURRENT_LIABILITIES],
            self::BUDGETARY => [AccCategoryEnum::INCOME, AccCategoryEnum::EXPENSE],
            self::REGULATORY => [AccCategoryEnum::REGULATORY_ACCOUNTS],
        };
    }

    public function getAccCategoryValues()
    {
        $accCategories = $this->getAccCategories();

        return collect($accCategories)->map(function ($item) {
            return $item->value;
        })->toArray();
    }

    public static function getAllLabelAndValues()
    {
        return collect(self::cases())->map(function ($item) {
            return $item->getLabelAndValue();
        });

    }
}
