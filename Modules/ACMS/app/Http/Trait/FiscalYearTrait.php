<?php

namespace Modules\ACMS\app\Http\Trait;

use Modules\ACMS\app\Models\FiscalYear;
use Morilog\Jalali\Jalalian;

trait FiscalYearTrait
{


    public function createFiscalYear(array $data)
    {
        $data = $this->fiscalYearDataPreparation($data);
        $fiscalYear = FiscalYear::firstOrCreate($data->toArray()[0]);
        return $fiscalYear;

    }

    public function fiscalYearDataPreparation(array $data)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) {

            $startDate = convertJalaliPersianCharactersToGregorian($item['startDate']);
            $finishDate = Jalalian::fromFormat('Y/m/d', $item['finishDate'])->getEndDayOfYear()->toCarbon()->toDateTimeString();

            return [
                'name' => $item['fiscalYearName'],
                'start_date' => $startDate,
                'finish_date' => $finishDate,
            ];
        });

        return $data;

    }
}
