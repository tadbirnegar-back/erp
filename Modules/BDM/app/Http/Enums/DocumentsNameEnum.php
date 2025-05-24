<?php

namespace Modules\BDM\app\Http\Enums;

enum DocumentsNameEnum: string
{
    case FORM_ALEF_ONE_TWO = 'فرم الف ۲-۱';
    case FORM_THREE = 'فرم شماره ۳';

    case PLAN = "نقشه ساختمان";
    case MALEKIYATE_ZAMIN = 'نامه گواهی مالکیت زمین';
    case FORM_FIVE = 'فرم ۵: برگ تعهد معماری';
    case FORM_SIX = 'فرم ۶: برگ تعهد محاسبات سازه';
    case FORM_SEVEN = 'فرم ۷: برگ تعهد نظارت';
    case FORM_EIGHT = 'فرم ۸: اعلام شروع به کار عملیات ساختمانی';

    case TaxesBillPDF = 'صورت حساب قبض';

    case BuildingDossierPDF = 'پروانه ساختمانی';

    case FoundationConcreteLayingPDF = 'گزارش بتن ریزی پی';

    case HardeningSofteningStructurePDF = 'گزارش سفت کاری و نازک کاری';

    case StructureSekeletonPDF = 'اجرای اسکلت و عملیات سازه‌ای';

    case FinalReportPDF = 'گزارش نهایی کار';

    case BuildingOperationProgressPDF = 'گزارش پیشرفت عملیات ساختمانی';

    case WorkOverReportPDF = 'گواهی پایان کار';
}
