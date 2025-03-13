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

//        $ounitFiscalYear = OunitFiscalYear::upsert($preparedData->toArray(), ['ounit_id', 'fiscal_year_id'], ['ounit_id', 'fiscal_year_id']);
//        foreach ($preparedData as $data) {
//            OunitFiscalYear::firstOrCreate(['ounit_id' => $data['ounit_id'], 'fiscal_year_id' => $data['fiscal_year_id']], $data,);
//        }
//        $ounitFiscalYearsResult = OunitFiscalYear::where('fiscal_year_id', $fiscalYear->id)
//            ->whereIntegerInRaw('ounit_id', $preparedData->pluck('ounit_id')->toArray())
//            ->get(['id']);
//
//        return $ounitFiscalYearsResult;

        // Prepare your data
//        $preparedData = $this->ounitFiscalYearDataPreparation($data, $fiscalYear, $user);

// Step 1: Get the list of ounit_ids from your prepared data
        $ounitIds = $preparedData->pluck('ounit_id')->toArray();

// Fetch existing records for this fiscal year
        $existingOunits = OunitFiscalYear::where('fiscal_year_id', $fiscalYear->id)
            ->whereIntegerInRaw('ounit_id', $ounitIds)
            ->pluck('ounit_id')
            ->toArray();

// Step 2: Filter out records that already exist
        $newRecords = array_filter($preparedData->toArray(), function ($record) use ($existingOunits) {
            return !in_array($record['ounit_id'], $existingOunits);
        });

// Step 3: Bulk insert new records if there are any
        if (!empty($newRecords)) {
            OunitFiscalYear::insert($newRecords);
        }

// Optionally, fetch and return the result records
        $ounitFiscalYearsResult = OunitFiscalYear::where('fiscal_year_id', $fiscalYear->id)
            ->whereIntegerInRaw('ounit_id', $ounitIds)
            ->get(['id']);

        return $ounitFiscalYearsResult;
    }

    public function ounitFiscalYearDataPreparation(array $data, FiscalYear $fiscalYear, User $user)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) use ($fiscalYear, $user) {


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
