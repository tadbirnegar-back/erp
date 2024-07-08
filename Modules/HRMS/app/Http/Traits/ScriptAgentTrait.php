<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\ScriptAgent;

trait ScriptAgentTrait
{
    public function createScriptAgent(array $data): ScriptAgent
    {
        $scriptAgent = new ScriptAgent();
        $scriptAgent->title = $data['title'];
        $scriptAgent->script_agent_type_id = $data['scriptAgentTypeID'];
        $scriptAgent->save();

        return $scriptAgent;
    }

    public function getSingleScriptAgent(int $id): ?ScriptAgent
    {
        return ScriptAgent::find($id);
    }

    public function getListOfScriptAgents()
    {
        return ScriptAgent::all();
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
}
