<?php

namespace Modules\PFM\app\Http\Enums;

enum ApplicationsForTablesEnum: string
{
    case AMLAK_MOSTAGHELAT_SINGLES = "عوارض سالیانه املاک و مستقلات(سطح روستا)";
    case AMLAK_MOSTAGHELAT_MULTIPLES = '';

    public function values(): array
    {
        return match ($this) {
            self::AMLAK_MOSTAGHELAT_SINGLES => [1, 4, 20],
            self::AMLAK_MOSTAGHELAT_MULTIPLES => [1=>[2 , 3] , 2=> [5,6,7,8,9,10,11,12,13] , 3=>[14,15,16,17,18] , 4=>[1=>[19,20,21,22,23,24] , 2=>[25]]],
        };
    }

}
