<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\ScriptAgentType;

trait ScriptAgentTypesTrait
{
    public function createScriptAgentType(array $data): ScriptAgentType
    {
        return ScriptAgentType::create($data);
    }

    public function getSingleScriptAgentType(int $id): ?ScriptAgentType
    {
        return ScriptAgentType::find($id);
    }

    public function getListOfScriptAgentTypes()
    {
        return ScriptAgentType::all();
    }

    public function updateScriptAgentType(int $id, array $data): ScriptAgentType
    {
        $scriptAgentType = ScriptAgentType::findOrFail($id);
        $scriptAgentType->update($data);
        return $scriptAgentType;
    }

    public function deleteScriptAgentType(int $id): bool
    {
        $scriptAgentType = ScriptAgentType::findOrFail($id);
        return $scriptAgentType?->delete();
    }
}
