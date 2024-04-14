<?php

namespace Modules\HRMS\app\Http\Repositories;

use Illuminate\Support\Collection;
use Mockery\Exception;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

class RecruitmentScriptRepository
{
    public static function store(array|collection $data, int $employeeID)
    {
        try {
            $dataToInsert = self::dataPreparation($data, $employeeID);
            $status = RecruitmentScript::GetAllStatuses()->where('name', '=', 'فعال')->first();
            $result = [];
            foreach ($dataToInsert as $item) {
                $ounit = RecruitmentScript::create($item);
                $ounit->status()->attach($status->id);
                $result[] = $ounit;

            }
            return $result;
        }catch (Exception $e){
            return $e;
        }

    }

    private static function dataPreparation(array|collection $data, int $employeeID)
    {
        if (!$data instanceof Collection) {
            $data = collect($data);
        }

        $data = $data->map(function ($RS) use ($employeeID) {
            return [
                'employee_id' => $employeeID,
                'organization_unit_id' => $RS['ounitID'],
                'level_id' => $RS['levelID'],
                'position_id' => $RS['positionID'],

            ];
        });
        return $data;
    }
}
