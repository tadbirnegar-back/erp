<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\HRMS\app\Http\Enums\ProceduresEnum;
use Modules\HRMS\app\Models\ConfirmationTypeScriptType;
use Modules\HRMS\app\Models\ScriptType;

trait ScriptTypeTrait
{
    private string $activeStatus = 'فعال';
    private string $inactiveStatus = 'غیرفعال';

    public function createScriptType(array $data): ScriptType
    {
        $scriptType = new ScriptType();
        $scriptType->title = $data['title'];
        $scriptType->issue_time_id = $data['issueTimeID'] ?? null;
        $scriptType->employee_status_id = $data['employeeStatusID'] ?? null;
        $scriptType->save();

        $confirmationTypeScriptType = json_decode($data['confirmationTypes'], true);

        foreach ($confirmationTypeScriptType as $key => $item) {
            $cs = new ConfirmationTypeScriptType();
            $cs->script_type_id = $scriptType->id;
            $cs->confirmation_type_id = $item['confirmationTypeID'];
            $cs->option_id = $item['optionID'] ?? null;
            $cs->option_type = ProceduresEnum::from($item['confirmationTypeID'])->getOptionType();
            $cs->priority = $key + 1;
            $cs->save();
        }

        return $scriptType;
    }

    public function getSingleScriptType(int $id): ?ScriptType
    {
        return ScriptType::find($id);
    }

    public function getListOfScriptTypes()
    {
        return ScriptType::whereHas('status', function ($query) {
            $query->where('name', $this->activeStatus);
        })->with('issueTime', 'employeeStatus', 'confirmationTypes')->get();
    }

    public function updateScriptType(ScriptType $scriptType, array $data): ScriptType
    {
        $scriptType->title = $data['title'] ?? $scriptType->title;
        $scriptType->issue_time_id = $data['issue_time_id'] ?? $scriptType->issue_time_id;
        $scriptType->employee_status_id = $data['employee_status_id'] ?? $scriptType->employee_status_id;
        $scriptType->save();

        return $scriptType;
    }

    public function deleteScriptType(ScriptType $scriptType)
    {
        $scriptType->status_id = $this->inactiveScriptTypeScript()->id;
        $scriptType->save();
        return $scriptType;
    }

    public function activeScriptTypeStatus()
    {
        return ScriptType::firstWhere('status_id', $this->activeStatus);
    }

    public function inactiveScriptTypeScript()
    {
        return ScriptType::firstWhere('status_id', $this->inactiveStatus);
    }

    private function confirmationScriptDataPreparation(ScriptType $scriptType, array|Collection $data)
    {
        if (is_array($data)) {
            $data = collect($data);
        }

        $data = $data->map(fn($key, $item) => [
            'id' => $item['id'] ?? null,
            'script_type_id' => $scriptType->id,
            'confirmation_type_id' => $item['confirmationTypeID'],
            'option_id' => $item['optionID'] ?? null,
            'option_type' => ProceduresEnum::from($item['confirmationTypeID'])->getOptionType(),
            'priority' => $key + 1,
        ]);

        return $data;
    }
}
