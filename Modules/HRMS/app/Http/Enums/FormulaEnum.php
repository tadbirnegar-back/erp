<?php

namespace Modules\HRMS\app\Http\Enums;

enum FormulaEnum: int
{
    case HOGHOUQ_PAAYEH = 1;
    case PAYEH_SANAVAT = 2;
    case FOOGHOOLADE_DARJEBANDI_DEHYARI = 3;
    case FOOGHOOLADE_ROOSTAHA_BOZORG = 4;
    case FOOGHOOLADE_MASOULIAT_SERPARASTI = 5;
    case FOOGHOOLADE_MADRAK_TAHSILI = 6;
    case FOOGHOOLADE_ISARGARI = 7;
    case FOOGHOOLADE_SAKHTI_KAR = 8;
    case FOOGHOOLADE_JAZB = 9;
    case FOOGHOOLADE_BADI_AB_O_HAVO = 10;
    case FOOGHOOLADE_NOBATKARI_SHABKARI = 11;
    case MAMORIAT = 12;
    case PADASH_MADDE_12_KHARID_LEBAAS = 13;
    case PADASH_MADDE_15_EIDI = 14;
    case PADASH_MADDE_7_PAYAN_KAR = 15;
    case PADASH_MADDE_18_AMOZESH = 16;
    case PADASH_MADDE_8_BAHREVARI_ARZIYABI = 17;
    case PADASH_MADDE_8_BAHREVARI_OMRANI = 18;
    case PADASH_MADDE_8_BAHREVARI_SARMAYEGOZARI_VA_DARAMADZA = 19;
    case HAQ_TAHL = 20;
    case HAQ_AAILEMANDI = 21;
    case HAQ_MASKAN = 22;
    case KOMAK_HAZEINE_AQLAM_MASRIFI = 23;
    case HOGHOUQ_SABET = 24;
    case HOGHOUQ_MASHMOL_BIMEH = 25;
    case JAM_E_HOGHOUQ = 26;
    case HAQ_BIMEH = 27;
    case MALYAT = 28;
    case JAM_E_KASURAT = 29;
    case HOGHOUQ_QABEL_PAYOUT = 30;
    case HOGHOUQ_QABEL_PAYOUT_NAHAYI = 31;
    case EZAFE_KARI = 32;

    public function getLabel(): string
    {
        return match ($this) {
            self::HOGHOUQ_PAAYEH => 'حقوق پایه',
            self::PAYEH_SANAVAT => 'پایه سنوات',
            self::FOOGHOOLADE_DARJEBANDI_DEHYARI => 'فوق العاده درجه بندی دهیاری',
            self::FOOGHOOLADE_ROOSTAHA_BOZORG => 'فوق‌العاده روستاهای بزرگ',
            self::FOOGHOOLADE_MASOULIAT_SERPARASTI => 'فوق‌العاده مسئولیت(سرپرستی)',
            self::FOOGHOOLADE_MADRAK_TAHSILI => 'فوق‌العاده مدرک تحصیلی',
            self::FOOGHOOLADE_ISARGARI => 'فوق‌العاده ایثارگری',
            self::FOOGHOOLADE_SAKHTI_KAR => 'فوق‌العاده سختی کار',
            self::FOOGHOOLADE_JAZB => 'فوق‌العاده جذب',
            self::FOOGHOOLADE_BADI_AB_O_HAVO => 'فوق‌العاده بدی آب‌وهوا',
            self::FOOGHOOLADE_NOBATKARI_SHABKARI => 'فوق‌العاده نوبت‌کاری و شب‌کاری',
            self::MAMORIAT => 'ماموریت',
            self::EZAFE_KARI => 'اضافه کاری',
            self::PADASH_MADDE_12_KHARID_LEBAAS => 'پاداش ماده 12 - خرید لباس',
            self::PADASH_MADDE_15_EIDI => 'پاداش ماده 15 - عیدی',
            self::PADASH_MADDE_7_PAYAN_KAR => 'پاداش ماده 7 - پایان کار',
            self::PADASH_MADDE_18_AMOZESH => 'پاداش ماده 18 - آموزش',
            self::PADASH_MADDE_8_BAHREVARI_ARZIYABI => 'پاداش ماده 8 - پاداش بهره وری (ارزیابی عملکرد دهیاری ها)',
            self::PADASH_MADDE_8_BAHREVARI_OMRANI => 'پاداش ماده 8 - پاداش بهره وری (میزان پیشرفت عملیات عمرانی)',
            self::PADASH_MADDE_8_BAHREVARI_SARMAYEGOZARI_VA_DARAMADZA
            => 'پاداش ماده 8 - پاداش بهره وری (اجرای طرح سرمایه گذاری و درآمدزا)',
            self::HAQ_TAHL => 'حق تاهل',
            self::HAQ_AAILEMANDI => 'حق عائله‌مندی',
            self::HAQ_MASKAN => 'حق مسکن',
            self::KOMAK_HAZEINE_AQLAM_MASRIFI => 'کمک هزینه اقلام مصرفی',
            self::HOGHOUQ_SABET => 'حقوق ثابت',
            self::HOGHOUQ_MASHMOL_BIMEH => 'حقوق مشمول بیمه',
            self::JAM_E_HOGHOUQ => 'جمع حقوق',
            self::HAQ_BIMEH => 'حق بیمه',
            self::MALYAT => 'مالیات',
            self::JAM_E_KASURAT => 'جمع کسورات',
            self::HOGHOUQ_QABEL_PAYOUT => 'حقوق قابل پرداخت',
            self::HOGHOUQ_QABEL_PAYOUT_NAHAYI => 'حقوق قابل پرداخت نهایی',
        };
    }

    public function getFnName(): string
    {
        return match ($this) {
            self::HOGHOUQ_PAAYEH => 'getBaseSalary',
            self::PAYEH_SANAVAT => 'getBaseYears',
            self::FOOGHOOLADE_DARJEBANDI_DEHYARI => 'getVillageDegreeExtra',
            self::FOOGHOOLADE_ROOSTAHA_BOZORG => 'getBigVillageExtra',
            self::FOOGHOOLADE_MASOULIAT_SERPARASTI => 'getVillageSupervisorExtra',
            self::FOOGHOOLADE_MADRAK_TAHSILI => 'getEducationExtra',
            self::FOOGHOOLADE_ISARGARI => 'getIsarExtra',
            self::FOOGHOOLADE_SAKHTI_KAR => 'getDifficultyOfWorkExtra',
            self::FOOGHOOLADE_JAZB => 'getJazbExtra',
            self::FOOGHOOLADE_BADI_AB_O_HAVO => 'getBadWheatherExtra',
            self::FOOGHOOLADE_NOBATKARI_SHABKARI => 'getNightShiftExtra',
            self::MAMORIAT => 'getMissionExtra',
            self::PADASH_MADDE_12_KHARID_LEBAAS => 'getClothesArticle12Extra',
            self::PADASH_MADDE_15_EIDI => 'getEidiArticle15Extra',
            self::PADASH_MADDE_7_PAYAN_KAR => 'getEndOfWorkExtra',
            self::PADASH_MADDE_18_AMOZESH => 'getEducationArticle18',
            self::PADASH_MADDE_8_BAHREVARI_ARZIYABI => 'getEvaluationArticle8',
            self::PADASH_MADDE_8_BAHREVARI_OMRANI => 'getOperationalExpanseExtra',
            self::PADASH_MADDE_8_BAHREVARI_SARMAYEGOZARI_VA_DARAMADZA
            => 'getProductivityBonusExtra',
            self::HAQ_TAHL => 'getMarriageExtra',
            self::HAQ_AAILEMANDI => 'getChildrenBonusExtra',
            self::HAQ_MASKAN => 'getRightOfHomeExtra',
            self::KOMAK_HAZEINE_AQLAM_MASRIFI => 'getAllowanceForConsumableItems',
            self::HOGHOUQ_SABET => 'getFixedSalary',
            self::HOGHOUQ_MASHMOL_BIMEH => 'getSalaryContainsInsurance',
            self::JAM_E_HOGHOUQ => 'getTotalSalary',
            self::HAQ_BIMEH => 'getInsuranceRight',
            self::MALYAT => 'getTaxes',
            self::JAM_E_KASURAT => 'getTotalDeductions',
            self::HOGHOUQ_QABEL_PAYOUT => 'getMustBePayedSalary',
            self::HOGHOUQ_QABEL_PAYOUT_NAHAYI => 'getFinalMustBePayedSalary',
            self::EZAFE_KARI => 'getOverTimeExtra',
        };
    }

    public function getLabelAndValue()
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->value
        ];
    }

    public function getPrice()
    {
        return match ($this) {
            self::HOGHOUQ_PAAYEH => '1000000',
            self::PAYEH_SANAVAT => '1200000',
        };
    }

    public static function formulaList()
    {
        $cats = collect(self::cases());

        $result = $cats->map(fn($item, $key) => [
            'value' => $item->value,
            'label' => $item->getLabel(),
        ]);

        return $result;
    }
}
