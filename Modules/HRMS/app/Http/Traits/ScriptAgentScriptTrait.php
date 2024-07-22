<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptAgent;
use Modules\HRMS\app\Models\ScriptAgentScript;

trait ScriptAgentScriptTrait
{
    public function sasStore(array $data, RecruitmentScript $script)
    {
        $preparedData = $this->prepareData($data, $script);
        $result = ScriptAgentScript::insert($preparedData);

        return $result;
    }

    private function prepareData(array|Collection $data, RecruitmentScript $script)
    {
        if (is_array($data)) {
            $data = collect($data);
        }
        $data = $data->map(function ($sas) use ($script) {
            return [
                'id' => $sas['sasID'] ?? null,
                'contract' => $sas['defaultValue'],
                'script_agent_id' => $sas['scriptAgentID'],
                'script_id' => $sas['scriptID'],
            ];
        });

        return $data->toArray();

    }
}
