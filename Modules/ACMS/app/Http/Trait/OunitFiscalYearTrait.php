<?php

namespace Modules\ACMS\app\Http\Trait;

use Modules\AAA\app\Models\User;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\ACMS\app\Models\OunitFiscalYear;

trait OunitFiscalYearTrait
{

    public function bulkStoreOunitFiscalYear(array $data, FiscalYear $fiscalYear, User $user)
    {
        $preparedData = $this->ounitFiscalYearDataPreparation($data, $fiscalYear, $user);

        $ounitFiscalYear = OunitFiscalYear::insert($preparedData->toArray());
        $ounitFiscalYearsResult = OunitFiscalYear::latest('id')
            ->take($preparedData->count())
            ->get(['id']);

        return $ounitFiscalYearsResult;
    }

    public function ounitFiscalYearDataPreparation(array $data, FiscalYear $fiscalYear, User $user)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) use ($fiscalYear, $user) {

            if (!isset($item['ounitID'])) {
                \Log::info($item);
            }
            return [
                'ounit_id' => $item['ounitID'],
                'fiscal_year_id' => $fiscalYear->id,
                'creator_id' => $user->id ?? null,
                'closer_id' => $item['closerID'] ?? null,
                'create_date' => now(),
                'close_date' => $item['closeDate'] ?? null,
            ];
        });

        return $data;
    }
}
