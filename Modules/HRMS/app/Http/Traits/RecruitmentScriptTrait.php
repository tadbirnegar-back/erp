<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\HRMS\app\Models\RecruitmentScript;

trait RecruitmentScriptTrait
{
    public function rsStore(array|collection $data, int $employeeID)
    {
        $dataToInsert = $this->rsDataPreparation($data, $employeeID);

        $status = $this->activeRsStatus();
        $result = [];
        foreach ($dataToInsert as $item) {
            $ounit = RecruitmentScript::create($item);
            $ounit->status()->attach($status->id);
            $result[] = $ounit;

        }
        return $result;


    }

    public function rsBulkUpdate(array|collection $data, int $employeeID)
    {
        $dataToUpsert = $this->rsDataPreparation($data, $employeeID);
        $insertCount = $dataToUpsert->where('id', null)->count();
        $result = RecruitmentScript::upsert($dataToUpsert->toArray(), ['id']

        );

        if ($insertCount > 0) {
            $activeStatus = $this->activeRsStatus();

            $rses = RecruitmentScript::orderBy('id', 'desc')->take($result)->get();
            $rses->map(fn(RecruitmentScript $recruitmentScript) => $recruitmentScript->status()->attach($activeStatus->id));
        }
        return $result;
    }

    public function rsDelete(array $data)
    {
        $rses = RecruitmentScript::find($data);
        $deleteStatus = $this->inActiveRsStatus();
        foreach ($rses as $item) {
            $item->status()->attach($deleteStatus->id);
        }

        return true;
    }


    private function rsDataPreparation(array|collection $data, int $employeeID)
    {
        if (is_array($data)) {
            $data = collect($data);
        }

        $data = $data->map(fn($RS) => [
            'id' => $RS['rsID'] ?? null,
            'employee_id' => $employeeID,
            'organization_unit_id' => $RS['ounitID'],
            'level_id' => $RS['levelID'],
            'position_id' => $RS['positionID'],
            'create_date' => $RS['rsDate'] ?? null,

        ]);
        return $data;
    }

    public function activeRsStatus()
    {
        return RecruitmentScript::GetAllStatuses()->firstWhere('name', '=', 'غیرفعال');
    }

    public function inActiveRsStatus()
    {
        return RecruitmentScript::GetAllStatuses()->firstWhere('name', '=', 'غیرفعال');
    }
}
