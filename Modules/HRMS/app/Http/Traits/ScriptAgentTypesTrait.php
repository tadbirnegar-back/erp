<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\HRMS\app\Models\ScriptAgentType;

trait ScriptAgentTypesTrait
{
    private string $ScriptAgentTypeActiveName = 'فعال';
    private string $ScriptAgentTypeInactiveName = 'حذف شده';

    public function createScriptAgentType(array $data): ScriptAgentType
    {
        $scriptAgentType = new ScriptAgentType();
        $scriptAgentType->title = $data['title'];
        $status = $this->activeScriptAgentTypeStatus();
        $scriptAgentType->status_id = $status->id;
        $scriptAgentType->save();

        return $scriptAgentType;
    }

    public function getSingleScriptAgentType(int $id): ?ScriptAgentType
    {
        return ScriptAgentType::find($id);
    }

    public function getListOfScriptAgentTypes()
    {
//        $status = $this->activeScriptAgentTypeStatus();

        return ScriptAgentType::whereHas('status', function ($query) {
            $query->where('name', '=', $this->ScriptAgentTypeActiveName);
        })->get();
    }

    public function updateScriptAgentType(ScriptAgentType $scriptAgentType, array $data): ScriptAgentType
    {

        $scriptAgentType->title = $data['title'];
        $scriptAgentType->save();

        return $scriptAgentType;
    }

    public function deleteScriptAgentType(ScriptAgentType $scriptAgentType): ScriptAgentType
    {
        $status = $this->inactiveScriptAgentTypeStatus();
        $scriptAgentType->status_id = $status->id;
        $scriptAgentType->save();

        return $scriptAgentType;
    }

    public function activeScriptAgentTypeStatus()
    {
        return Cache::rememberForever('script_agent_type_active_status', function () {
            return ScriptAgentType::GetAllStatuses()
                ->firstWhere('name', '=', $this->ScriptAgentTypeActiveName);
        });
    }

    public function inactiveScriptAgentTypeStatus()
    {
        return Cache::rememberForever('script_agent_type_inactive_status', function () {
            return ScriptAgentType::GetAllStatuses()
                ->firstWhere('name', '=', $this->ScriptAgentTypeInactiveName);
        });
    }
}
