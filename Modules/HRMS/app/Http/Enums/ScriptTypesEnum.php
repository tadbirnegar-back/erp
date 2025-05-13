<?php

namespace Modules\HRMS\app\Http\Enums;

enum ScriptTypesEnum: string
{
    case MASOULE_FAANI = 'استخدام مسئول فنی';
    case VILLAGER = 'انتصاب دهیار';
    case FINANCIAL = 'استخدام مسئول مالی دهیاری';

    public function getCalculateClassPrefix()
    {
        return match ($this) {
            self::MASOULE_FAANI => 'TechnicalOfficer',
            self::VILLAGER => 'Villager',
            self::FINANCIAL => 'Financial',
        };
    }
}
