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
}
