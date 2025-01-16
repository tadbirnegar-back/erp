<?php

namespace Modules\ACMS\app\Http\Enums;

use Modules\ACC\app\Http\Enums\AccCategoryEnum;

enum SubjectTypeEnum: int
{
    case INCOME = 1;
    case ECONOMIC_EXPENSE = 2;
    case OPERATIONAL_EXPENSE = 3;

    public function getLabel()
    {
        return match ($this) {
            self::INCOME => 'منابع درآمدی',
            self::ECONOMIC_EXPENSE => 'طبقه بندی اقتصادی',
            self::OPERATIONAL_EXPENSE => 'طبقه بندی عملیاتی',
        };

    }

    public function getLabelAndValue()
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->value,
        ];

    }

    public function getCategoryEnum()
    {
        return match ($this) {
            self::INCOME => AccCategoryEnum::INCOME,
            self::ECONOMIC_EXPENSE, self::OPERATIONAL_EXPENSE => AccCategoryEnum::EXPENSE,
        };

    }

    public static function getAllLabelsAndValues()
    {
        return collect(self::cases())->map(function ($item) {
            return $item->getLabelAndValue();
        });

    }
}
