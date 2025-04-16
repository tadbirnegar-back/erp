<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptAgentScript;

trait ScriptAgentScriptTrait
{
    public function sasStore(array $data, RecruitmentScript $script)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $preparedData = $this->prepareData($data, $script);
        $result = ScriptAgentScript::insert($preparedData->toArray());


        return $result;
    }

    private function prepareData(array|Collection $data, RecruitmentScript $script)
    {
        if (is_array($data)) {
            $data = collect($data);
        }
        $data = $data->map(function ($sas) use ($script) {
            $scriptID = $script->id;
            return [
                'id' => $sas['sasID'] ?? null,
                'contract' => $sas['defaultValue']??0,
                'script_agent_id' => $sas['scriptAgentID']??3,
                'script_id' => $scriptID,
            ];
        });

        return $data;

    }
}
