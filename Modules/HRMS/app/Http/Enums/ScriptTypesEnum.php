<?php

namespace Modules\HRMS\app\Http\Enums;

enum ScriptTypesEnum: string
{
    case MASOULE_FAANI = 'استخدام مسئول فنی';
    case VILLAGER = 'انتصاب دهیار';
    case FINANCIAL = 'استخدام مسئول مالی دهیاری';
    case APPOINT_AZAM_COMMITTEE = 'انتصاب هیئت تطبیق';
    case APPOINT_BAKHSHDAR = 'انتصاب بخشدار';
    case APPOINT_SHORA = 'انتصاب شورا';
    case APPOINT_SECRETARY = 'انتصاب دبیر';
    case APPOINT_SARPARAST_DEHIYARI = 'انتصاب سرپرست دهیاری';
    case APPOINT_HR_MANAGER = 'انتصاب مدیر منابع انسانی';
    case APPOINT_SECRETARY_FREE_ZONE = 'انتصاب دبیر منطقه آزاد';
    case APPOINT_FREE_ZONE_CHAIRMAN = 'انتصاب رئیس منطقه آزاد';
    case APPOINT_AZAM_COMMITTEE_FREE_ZONE = 'انتصاب هیئت تطبیق منطقه آزاد';
    case HIRE_FIRE_FIGHTER = 'استخدام آتشنشان';

    public function getCalculateClassPrefix()
    {
        return match ($this) {
            self::MASOULE_FAANI => 'TechnicalOfficer',
            self::VILLAGER => 'Villager',
            self::FINANCIAL => 'Financial',
            default => null,
        };
    }
}
