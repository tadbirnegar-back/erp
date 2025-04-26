<?php

namespace Modules\PFM\app\Http\Enums;

enum LeviesListEnum: string
{
    case AMLAK_MOSTAGHELAT = "عوارض سالیانه املاک و مستقلات(سطح روستا)";
    case TAFKIK_ARAZI = "عوارض تفکیک اراضی";
    case CHESHME_MADANI = "عوارض چشمه های معدنی";
    case DIVAR_KESHI = "عوارض صدور مجوز حصارکشی(دیوارگذاری) برای املاک فاقد مستحدثات";
    case ZIRBANA_MASKONI = "عوارض احداث زیربنا(در حد تراکم) مسکونی غیرمسکونی";
    case BALKON_PISH_AMADEGI = "عوارض بر بالکن و پیش آمدگی";
    case MOSTAHADESAT_MAHOVATEH = "عوارض مستحدثات واقع در محوطه املاک(آلاچیق،پارکینگ مسقف،استخر)";
    case TAMDID_PARVANEH_SAKHTEMAN = "عوارض تمدید پروانه های ساختمانی";
    case TAJDID_PARVANEH_SAKHTEMAN = "عوارض تجدید پروانه ساختمانی";
    case GHAT_DERAKHTAN = "عوارض قطع درختان";
    case MASHAGHEL_DAEM = "عوارض بر مشاغل(دائم)";
    case TASISAT_ROOSTAEI = "عوارض صدور مجوز احداث و نصب تأسیسات روستایی";
    case TABLIGHAT = "عوارض بر تابلوهای تبلیغاتی";
    case ARZESHE_AFZODEH_HADI = "عوارض ارزش افزوده ناشی از الحاق به بافت و تغییر کاربری عرصه در اجرای طرح هادی روستایی";
    case ARZESHE_AFZODEH_OMRAN = "عوارض ارزش افزوده ناشی از اجرای طرح های عمران روستایی";
    case BAHAYE_KHEDMAT = "عناوین بهای خدمت";
}
