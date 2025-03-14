<?php

namespace Modules\ACC\app\Http\Enums;

enum DocumentTypeEnum: int
{
    case NORMAL = 1;
    case TEMPORARY = 2;
    case OPENING = 3;
    case CLOSING = 4;


    public function getLabel()
    {
        return match ($this) {
            self::TEMPORARY => 'بستن سند موقت',
            self::NORMAL => 'سند معمولی',
            self::OPENING => 'سند افتتاحیه',
            self::CLOSING => 'سند اختتامیه',
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
        })->toArray();
    }


}
