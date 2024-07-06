<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\ScriptAgent;

trait ScriptAgentTrait
{
    public function createScriptAgent(array $data): ScriptAgent
    {
        return ScriptAgent::create($data);
    }

    public function getSingleScriptAgent(int $id): ?ScriptAgent
    {
        return ScriptAgent::find($id);
    }

    public function getListOfScriptAgents()
    {
        return ScriptAgent::all();
    }

    public function updateScriptAgent(int $id, array $data): ScriptAgent
    {
        $scriptAgent = ScriptAgent::findOrFail($id);
        $scriptAgent->update($data);
        return $scriptAgent;
    }

    public function deleteScriptAgent(int $id): bool
    {
        $scriptAgent = ScriptAgent::findOrFail($id);
        return $scriptAgent?->delete();
    }
}
