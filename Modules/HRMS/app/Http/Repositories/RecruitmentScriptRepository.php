<?php

namespace Modules\HRMS\app\Http\Repositories;

use Illuminate\Support\Collection;
use Mockery\Exception;
use Modules\HRMS\app\Models\RecruitmentScript;

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
        } catch (Exception $e) {
            return $e;
        }

    }

    public static function bulkUpdate(array|collection $data, int $employeeID)
    {
        $dataToUpsert = self::dataPreparation($data, $employeeID);
        $result = RecruitmentScript::upsert($dataToUpsert->toArray(), ['id']);
        return $result;
    }

    public static function delete(array $data)
    {
        $rses = RecruitmentScript::find($data);
        $deleteStatus = RecruitmentScript::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();
        foreach ($rses as $item) {
            $item->status()->attach($deleteStatus->id);
        }

        return true;
    }


    private static function dataPreparation(array|collection $data, int $employeeID)
    {
        if (!$data instanceof Collection) {
            $data = collect($data);
        }

        $data = $data->map(function ($RS) use ($employeeID) {
            return [
                'id' => $RS['rsID'] ?? null,
                'employee_id' => $employeeID,
                'organization_unit_id' => $RS['ounitID'],
                'level_id' => $RS['levelID'],
                'position_id' => $RS['positionID'],
                'create_date' => $RS['rsDate'] ?? null,

            ];
        });
        return $data;
    }
}
