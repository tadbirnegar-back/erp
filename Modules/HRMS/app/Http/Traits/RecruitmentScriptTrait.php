<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\HRMS\app\Models\ConfirmationType;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptAgentScript;

trait RecruitmentScriptTrait
{
    public function rsStore(array|collection $data, int $employeeID)
    {
        $dataToInsert = $this->rsDataPreparation($data, $employeeID);

        $status = $this->pendingRsStatus();
        $result = [];
        foreach ($dataToInsert as $key => $item) {
            $rs = RecruitmentScript::create($item);
            $rs->status()->attach($status->id);
            $agents = json_decode($data[$key], true);
            foreach ($agents as $agent) {
                $scriptAgentScript = ScriptAgentScript::create([
                    'contract' => $agent['contract'],
                    'script_id' => $rs->id,
                    'script_agent_id' => $agent['agentID'],
                ]);

                $rs->setAttribute('scriptAgentScript', $scriptAgentScript);
            }
            $rs->load('scriptType.confirmationTypes');

            $conformationTypes = $rs->scriptType->confirmationTypes;

            $conformationTypes->each(function (ConfirmationType $confirmationType) {
                 $confirmationType->pivot->option_id;
                 $confirmationType->pivot->option_type;
            });
            $result[] = $rs;
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
            'description' => $RS['description'] ?? null,
            'hire_type_id' => $RS['hireTypeID'] ?? null,
            'job_id' => $RS['jobID'] ?? null,
            'operator_id' => $RS['operatorID'] ?? null,
            'script_type_id' => $RS['scriptTypeID'] ?? null,
            'start_date' => $RS['startDate'] ?? null,
            'expire_date' => $RS['expireDate'] ?? null,
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

    public function pendingRsStatus()
    {
        return RecruitmentScript::GetAllStatuses()->firstWhere('name', '=', 'در انتظار');
    }
}
