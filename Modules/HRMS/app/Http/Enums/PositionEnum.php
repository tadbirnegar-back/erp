<?php

namespace Modules\HRMS\app\Http\Enums;

enum PositionEnum: string
{
    case DEHIYAR = 'دهیار';
    case SARPARAST_DEHIYARI = 'سرپرست دهیاری';
    case BAKHSHDAR = 'بخشدار';
    case KARSHENAS_OSTANDEHARI = 'کارشناس استانداری';
    case SHORA_E_ROSTA = 'شورای روستا';
    case KARSHENAS_MOSHAVAREH = 'کارشناس مشورتی';
    case OZV_HEYAAT = 'عضو هیئت';
    case MASOOL_DABIRKHANEH = 'مسئول دبیرخانه';
    case NAIBB_E_QOHE_QAZAEIEH = 'نماینده قوه قضائیه';
    case SHORA_E_SHAHRESTAN_MEMBER = 'عضو شورای شهرستان';
    case NAIBB_E_OSTANDEHARI = 'نماینده استانداری';
    case MODIR_MANABE_ENSANI = 'مدیر منابع انسانی';
    case MASOOL_DABIRKHANEH_MANATEGH_AZAD = 'مسئول دبیرخانه منطقه آزاد';
    case KARSHENAS_MOSHAVAREH_MANATEGH_AZAD = 'کارشناس مشورتی منطقه آزاد';
    case NAIBB_E_QOHE_QAZAYIEH_MANATEGH_AZAD = 'نماینده قوه قضاییه منطقه آزاد';
    case NAIBB_E_OSTANDEHARI_MANATEGH_AZAD = 'نماینده استانداری منطقه آزاد';
    case RAIS_MANATEGH_AZAD = 'رئیس منطقه آزاد';
    case SHORA_E_SHAHRESTAN_MEMBER_MANATEGH_AZAD = 'عضو شورای شهرستان منطقه آزاد';
    case MASOOL_FANI = 'مسئول فنی';
    case MODIR_AMOOZESH_DIGITAL = 'مدیر آموزش دیجیتال';
    case MASOOL_MALI = 'مسئول مالی';
    case RANANDEH_ATESHNESHANI = 'راننده آتشنشانی';
    case KARMANDEH_ATESHNESHANI = 'کارمند آتشنشانی';
    case MASOOL_FANI_DEHIYARI = 'مسئول فنی دهیاری';

    public function getJob()
    {
        $job = JobEnum::tryFrom($this->value);

        if (!$job) {
            $job = match ($this) {
                self::SARPARAST_DEHIYARI => JobEnum::DEHIYAR,
                self::SHORA_E_ROSTA => JobEnum::SHORA,
                self::NAIBB_E_QOHE_QAZAEIEH, self::SHORA_E_SHAHRESTAN_MEMBER, self::SHORA_E_SHAHRESTAN_MEMBER_MANATEGH_AZAD => JobEnum::OZV_HEYAAT,
                self::NAIBB_E_OSTANDEHARI, self::NAIBB_E_OSTANDEHARI_MANATEGH_AZAD, self::NAIBB_E_QOHE_QAZAYIEH_MANATEGH_AZAD, self::KARSHENAS_MOSHAVAREH_MANATEGH_AZAD => JobEnum::KARSHENAS_MOSHAVAREH,
                self::MASOOL_DABIRKHANEH_MANATEGH_AZAD => JobEnum::MASOOL_DABIRKHANEH,
                self::MASOOL_FANI_DEHIYARI => JobEnum::MASOOL_FANI,
            };
        }

        return $job;

    }

    public function getScriptType()
    {
        return match ($this) {
            self::DEHIYAR => ScriptTypesEnum::VILLAGER,
            self::BAKHSHDAR => ScriptTypesEnum::APPOINT_BAKHSHDAR,
            self::KARSHENAS_OSTANDEHARI => ScriptTypesEnum::APPOINT_AZAM_COMMITTEE,
            self::SHORA_E_ROSTA => ScriptTypesEnum::APPOINT_SHORA,
            self::KARSHENAS_MOSHAVAREH => ScriptTypesEnum::APPOINT_AZAM_COMMITTEE,
            self::OZV_HEYAAT => ScriptTypesEnum::APPOINT_AZAM_COMMITTEE,
            self::MASOOL_DABIRKHANEH => ScriptTypesEnum::APPOINT_SECRETARY,
            self::SARPARAST_DEHIYARI => ScriptTypesEnum::APPOINT_SARPARAST_DEHIYARI,
            self::NAIBB_E_QOHE_QAZAEIEH => ScriptTypesEnum::APPOINT_AZAM_COMMITTEE,
            self::SHORA_E_SHAHRESTAN_MEMBER => ScriptTypesEnum::APPOINT_AZAM_COMMITTEE,
            self::NAIBB_E_OSTANDEHARI => ScriptTypesEnum::APPOINT_AZAM_COMMITTEE,
            self::MODIR_MANABE_ENSANI => ScriptTypesEnum::APPOINT_HR_MANAGER,
            self::MASOOL_DABIRKHANEH_MANATEGH_AZAD => ScriptTypesEnum::APPOINT_SECRETARY_FREE_ZONE,
            self::KARSHENAS_MOSHAVAREH_MANATEGH_AZAD => ScriptTypesEnum::APPOINT_AZAM_COMMITTEE_FREE_ZONE,
            self::NAIBB_E_QOHE_QAZAYIEH_MANATEGH_AZAD => ScriptTypesEnum::APPOINT_AZAM_COMMITTEE_FREE_ZONE,
            self::NAIBB_E_OSTANDEHARI_MANATEGH_AZAD => ScriptTypesEnum::APPOINT_AZAM_COMMITTEE_FREE_ZONE,
            self::RAIS_MANATEGH_AZAD => ScriptTypesEnum::APPOINT_FREE_ZONE_CHAIRMAN,
            self::SHORA_E_SHAHRESTAN_MEMBER_MANATEGH_AZAD => ScriptTypesEnum::APPOINT_AZAM_COMMITTEE_FREE_ZONE,
            self::MASOOL_FANI => ScriptTypesEnum::MASOULE_FAANI,
            self::MODIR_AMOOZESH_DIGITAL => ScriptTypesEnum::APPOINT_HR_MANAGER, // or custom logic if needed
            self::MASOOL_MALI => ScriptTypesEnum::MASOULE_FAANI,
            self::RANANDEH_ATESHNESHANI => ScriptTypesEnum::HIRE_FIRE_FIGHTER,
            self::KARMANDEH_ATESHNESHANI => ScriptTypesEnum::HIRE_FIRE_FIGHTER,

            // Default fallback
            default => null,
        };
    }
}
