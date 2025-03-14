<?php

namespace Modules\BNK\app\Http\Enums;

enum BankAccountTypeEnum: int
{
    case CURRENT = 1;
    case SAVING = 2;

    public function getLabel()
    {
        return match ($this) {
            self::CURRENT => 'جاری',
            self::SAVING => 'قرض الحسنه',
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
        $cases = collect(self::cases())->map(function ($case) {
            return $case->getLabelAndValue();
        });

        return $cases->toArray();

    }
}
