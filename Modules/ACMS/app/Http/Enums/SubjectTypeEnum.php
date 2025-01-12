<?php

namespace Modules\ACMS\app\Http\Enums;

use Modules\ACC\app\Http\Enums\AccCategoryEnum;

enum SubjectTypeEnum: int
{
    case INCOME = 1;
    case EXPENSE = 2;

    public function getLabel()
    {
        return match ($this) {
            self::INCOME => 'درآمد',
            self::EXPENSE => 'هزینه',
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
            self::EXPENSE => AccCategoryEnum::EXPENSE,
        };

    }

    public static function getAllLabelsAndValues()
    {
        return collect(self::cases())->map(function ($item) {
            return $item->getLabelAndValue();
        });

    }
}
