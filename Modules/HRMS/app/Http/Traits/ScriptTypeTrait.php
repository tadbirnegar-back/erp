<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\ScriptType;

trait ScriptTypeTrait
{
    public function createScriptType(array $data): ScriptType
    {
        $scriptType = new ScriptType();
        $scriptType->title = $data['title'];
        $scriptType->issue_time_id = $data['issue_time_id'] ?? null;
        $scriptType->employee_status_id = $data['employee_status_id']?? null;
        $scriptType->save();

        return $scriptType;
    }

    public function getSingleScriptType(int $id): ?ScriptType
    {
        return ScriptType::find($id);
    }
    public function getListOfScriptTypes()
    {
        return ScriptType::with('issueTime','employeeStatus','confirmationTypes')->get();
    }
    public function updateScriptType(ScriptType $scriptType, array $data): ScriptType
    {
        $scriptType->title = $data['title'] ?? $scriptType->title;
        $scriptType->issue_time_id = $data['issue_time_id'] ?? $scriptType->issue_time_id;
        $scriptType->employee_status_id = $data['employee_status_id'] ?? $scriptType->employee_status_id;
        $scriptType->save();

        return $scriptType;
    }

    public function deleteScriptType(int $id): bool
    {
        $scriptType = ScriptType::findOrFail($id);
        return $scriptType?->delete();
    }
}
