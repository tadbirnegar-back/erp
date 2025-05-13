<?php

namespace Modules\HRMS\App\Http\Enums;

enum JobEnum: string
{
    case BAKHSHDAR = 'بخشدار';
    case DEHIYAR = 'دهیار';
    case FARMANDAR = 'فرماندار';
    case SHORA = 'شورا';
    case KARSHENAS_MOSHAVAREH = 'کارشناس مشورتی';
    case OZV_HEYAAT = 'عضو هیئت';
    case MASOOL_DABIRKHANEH = 'مسئول دبیرخانه';
    case SARPARAST_DEHIYARI = 'سرپرست دهیاری';
    case KARSHENAS_OSTANDEHARI = 'کارشناس استانداری';
    case MODIR_MANABE_ENSANI = 'مدیر منابع انسانی';
    case RAIS_MANATEGH_AZAD = 'رئیس منطقه آزاد';
    case MASOOL_FANI = 'مسئول فنی';
    case MODIR_AMOOZESH_DIGITAL = 'مدیر آموزش دیجیتال';
    case MASOOL_MALI = 'مسئول مالی';
    case RANANDEH_ATESHNANEE = 'راننده آتشنانی';
    case ATESHNESHAN = 'آتشنشان';
    case KARMANDEH_ATESHNANEE = 'کارمند آتشنانی';
}
