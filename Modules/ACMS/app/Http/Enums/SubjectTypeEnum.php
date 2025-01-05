<?php

namespace Modules\ACMS\app\Http\Enums;

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

    public static function getAllLabelsAndValues()
    {
        return collect(self::cases())->map(function ($item) {
            return $item->getLabelAndValue();
        });

    }
}
