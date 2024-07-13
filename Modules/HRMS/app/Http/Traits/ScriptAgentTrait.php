<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\HRMS\app\Models\ScriptAgent;
use Modules\HRMS\app\Models\ScriptAgentCombo;

trait ScriptAgentTrait
{
    private string $activeStatus = 'فعال';
    private string $inactiveStatus = 'غیرفعال';

    public function createScriptAgent(array $data): ScriptAgent
    {
        $scriptAgent = new ScriptAgent();
        $scriptAgent->title = $data['title'];
        $scriptAgent->script_agent_type_id = $data['scriptAgentTypeID'];
        $scriptAgent->status_id = $this->activeScriptAgentStatus()->id;
        $scriptAgent->save();

        $combos = json_decode($data['combos'], true);
        $preparedCombos = $this->comboDataPreparation($combos, $scriptAgent);
        ScriptAgentCombo::insert($preparedCombos->toArray());

        return $scriptAgent;
    }

    public function getSingleScriptAgent(int $id): ?ScriptAgent
    {
        return ScriptAgent::find($id);
    }

    public function getListOfScriptAgents()
    {
        $scriptAgents= ScriptAgent::whereHas('status', function ($query) {
            $query->where('name', $this->activeStatus);
        })->with('scriptTypes.pivot.hireType')->get();

        return $scriptAgents;
    }

    public function updateScriptAgent(ScriptAgent $scriptAgent, array $data): ScriptAgent
    {
        $scriptAgent->title = $data['title'] ?? $scriptAgent->title;
        $scriptAgent->script_agent_type_id = $data['scriptAgentTypeID'] ?? $scriptAgent->script_agent_type_id;
        $scriptAgent->save();

        return $scriptAgent;
    }

    public function deleteScriptAgent(int $id): bool
    {
        $scriptAgent = ScriptAgent::findOrFail($id);
        return $scriptAgent->delete();
    }

    private function comboDataPreparation(array|Collection $data, ScriptAgent $scriptAgent)
    {
        if (is_array($data)) {
            $data = collect($data);
        }

        $data = $data->map(function ($item) use ($scriptAgent) {
            return [
                'id' => $item['comboID'] ?? null,
                'default_value' => $item['defaultValue'] ?? null,
                'formula' => $item['formulaID'] ?? null,
                'script_agent_id' => $scriptAgent->id,
                'hire_type_id' => $item['hireTypeID'],
                'script_type_id' => $item['scriptTypeID'],
            ];
        });

        return $data;
    }

    public function activeScriptAgentStatus()
    {
        return ScriptAgent::GetAllStatuses()->firstWhere('name', $this->activeStatus);
    }

    public function inactiveScriptAgentStatus()
    {
        return ScriptAgent::GetAllStatuses()->firstWhere('name', $this->inactiveStatus);
    }
}
