<?php

namespace Modules\ODOC\app\Http\Enums;

use Modules\BDM\app\Models\BuildingDossier;

enum OdocDocumentComponentsTypeEnum: string
{
    case BONYADE_MASKAN = 'HousingFoundationPDF';

    case FORM_2 = "BuildingDossierIssuancePDF";

    case FORM_3 = "LandOwnershipPDF";

    case MEMAR_OBLIGATION = "ArchitecturalCommitmentPDF";

    case MOHASEB_OBLIGATION = "StructuralCalculationsCommitmentPDF";

    case NAZER_OBLIGATION = "OverseeringCommitmentPDF";

    case StartWorkingObligation = "StartOperationPDF";

    case BUILDING_PLAN = "BuildingPlanPDF";

    case TaxesBillPDF = "TaxBillPDF";

    public static function getName($name): string
    {
        return match ($name) {
            self::BONYADE_MASKAN->value => 'نامه استعلام از بنیاد مسکن',
            self::FORM_2->value => 'درخواست صدور پروانه',
            self::FORM_3->value => 'کواهی مالکیت زمین',
            self::MEMAR_OBLIGATION->value => 'فرم تعهد معمار',
            self::MOHASEB_OBLIGATION->value => 'فرم تعهد محاسبات سازه',
            self::NAZER_OBLIGATION->value => 'فرم تعهد نظارت',
            self::BUILDING_PLAN->value => 'دستور تهیه نقشه',
            self::StartWorkingObligation->value => 'اعلام شروع به کار عملیات ساختمانی',
            self::TaxesBillPDF->value => 'صورت حساب قیض',
        };
    }
}
