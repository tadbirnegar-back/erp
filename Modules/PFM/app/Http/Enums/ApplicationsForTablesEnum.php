<?php

namespace Modules\PFM\app\Http\Enums;

enum ApplicationsForTablesEnum: string
{
    case AMLAK_MOSTAGHELAT_SINGLES = "عوارض سالیانه املاک و مستقلات(سطح روستا)";
    case AMLAK_MOSTAGHELAT_MULTIPLES = "noNeed1";


    case TAFKIK_ARAZI_SINGLES = "عوارض تفکیک اراضی";
    case TAFKIK_ARAZI_MULTIPLES = "noNeed2";


    case PARVANEH_HESAR_SINGLES = "عوارض صدور مجوز حصارکشی(دیوارگذاری) برای املاک فاقد مستحدثات";
    case PARVANEH_HESAR_MULTIPLES = "noNeed3";

    case PARVANE_ZIRBANA_SINGLES = "عوارض احداث زیربنا(در حد تراکم) مسکونی غیرمسکونی";
    case PARVANE_ZIRBANA_MULTIPLES = "noNeed4";


    case PARVANE_BALKON_SINGLES = "عوارض بر بالکن و پیش آمدگی";
    case PARVANE_BALKON_MULTIPLES = "noNeed5";

    case PARVANEH_MOSTAHADESAT_SINGLES = "عوارض مستحدثات واقع در محوطه املاک(آلاچیق،پارکینگ مسقف،استخر)";
    case PARVANEH_MOSTAHADESAT_MULTIPLES = "noNeed6";



    public function values(): array
    {
        return match ($this) {
            self::AMLAK_MOSTAGHELAT_SINGLES => [1, 4],
            self::AMLAK_MOSTAGHELAT_MULTIPLES => [1 => [2, 3], 2 => [5, 6, 7, 8, 9, 10, 11, 12, 13], 3 => [14, 15, 16, 17, 18], 4 => [1 => [19, 20, 21, 22, 23, 24], 2 => [25]]],

            self::TAFKIK_ARAZI_SINGLES => [1, 4, 26, 27, 28, 29],
            self::TAFKIK_ARAZI_MULTIPLES => [1 => [2, 3], 2 => [5, 6, 7, 8, 9, 10, 11, 12, 13], 3 => [14, 15, 16, 17, 18], 4 => [1 => [19, 20, 21, 22, 23, 24], 2 => [25]]],


            self::PARVANEH_HESAR_SINGLES => [1, 4, 26],
            self::PARVANEH_HESAR_MULTIPLES => [1 => [2, 3], 2 => [5, 6, 7, 8, 9, 10, 11, 12, 13], 3 => [14, 15, 16, 17, 18], 4 => [1 => [19, 20, 21, 22, 23, 24], 2 => [25]]],

            self::PARVANE_ZIRBANA_SINGLES => [1, 4, 30],
            self::PARVANE_ZIRBANA_MULTIPLES => [1 => [2, 3], 2 => [12, 6, 11, 7, 8, 9], 3 => [23, 24, 21, 31, 32], 4 => [14, 27], 5 => [28, 29, 17]],

            self::PARVANE_BALKON_SINGLES => [1, 4],
            self::PARVANE_BALKON_MULTIPLES => [1 => [2, 3], 2 => [5, 6, 7, 8, 9, 10, 11, 12, 13], 3 => [14, 15, 16, 17, 18], 4 => [1 => [19, 20, 21, 22, 23, 24], 2 => [25]]],

            self::PARVANEH_MOSTAHADESAT_SINGLES => [1, 4, 26],
            self::PARVANEH_MOSTAHADESAT_MULTIPLES => [1 => [2, 3], 2 => [5, 6, 7, 8, 9, 10, 11, 12], 3 => [14, 15, 16, 17, 18], 4 => [1 => [19, 20, 21, 22, 23, 24], 2 => [25]]],

        };
    }

}
