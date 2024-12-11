<?php

namespace Modules\LMS\app\Http\Enums;

enum PrivaciesEnum: int
{
    case PRIVATE = 1;
    case PUBLIC = 2;

    public function getLabel()
    {
        return match ($this) {
            self::PRIVATE => 'خصوصی',
            self::PUBLIC => 'عمومی',
        };
    }

    public function getLabelAndValue()
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->value
        ];
    }

    public static function getAllLabelsAndValues()
    {
        return collect(self::cases())->map(function ($item) {
            return $item->getLabelAndValue();
        })->toArray();
    }
}
