<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\HRMS\app\Http\Enums\ProceduresEnum;
use Modules\HRMS\app\Models\ConfirmationTypeScriptType;
use Modules\HRMS\app\Models\ScriptType;

trait ScriptTypeTrait
{
    private string $activeScriptTypeStatus = 'فعال';
    private string $inactiveScriptTypeStatus = 'غیرفعال';

    public function createScriptType(array $data): ScriptType
    {
        $scriptType = new ScriptType();
        $scriptType->title = $data['title'];
        $scriptType->issue_time_id = $data['issueTimeID'] ?? null;
        $scriptType->origin_id = $data['typeOriginID'] ?? null;
        $scriptType->duration = $data['etebar'] ?? null;
        $scriptType->employee_status_id = $data['employeeStatusID'] ?? null;
        $scriptType->status_id = $this->activeScriptTypeStatus()->id;
        $scriptType->isHeadable = $data['isHeadable'] ?? false;
        $scriptType->save();

        $confirmationTypeScriptType = json_decode($data['confirmationTypes'], true);
        $preparedData = $this->confirmationScriptDataPreparation($scriptType, $confirmationTypeScriptType);

        ConfirmationTypeScriptType::insert($preparedData->toArray());
        $scriptType->load('employeeStatus', 'confirmationTypes');
        return $scriptType;
    }

    public function activeScriptTypeStatus()
    {
        return ScriptType::GetAllStatuses()->firstWhere('name', $this->activeScriptTypeStatus);
    }

    private function confirmationScriptDataPreparation(ScriptType $scriptType, array|Collection $data)
    {
        if (is_array($data)) {
            $data = collect($data);
        }
        $counter = 1; // Manual counter

        $data = $data->map(function ($item, $key) use ($scriptType, &$counter) {
            $procedure = ProceduresEnum::from($item['confirmationTypeID'])->getOptionType();
            $result = [
                'id' => $item['pivotID'] ?? null,
                'script_type_id' => $scriptType->id,
                'confirmation_type_id' => $item['confirmationTypeID'],
                'option_id' => $item['optionID'] ?? null,
                'option_type' => $procedure,
                'priority' => $counter,

            ];
            $counter++; // Increment the counter

            return $result;

        });

        return $data;
    }

    public function getSingleScriptType(int $id): ?ScriptType
    {
        return ScriptType::find($id);
    }

    public function getListOfScriptTypes()
    {
        return ScriptType::whereHas('status', function ($query) {
            $query->where('name', $this->activeScriptTypeStatus);
        })->with('employeeStatus', 'confirmationTypes')->get();
    }

    public function updateScriptType(ScriptType $scriptType, array $data): ScriptType
    {
        $scriptType->title = $data['title'] ?? $scriptType->title;
        $scriptType->issue_time_id = $data['issueTimeID'] ?? $scriptType->issue_time_id;
        $scriptType->origin_id = $data['typeOriginID'] ?? null;
        $scriptType->duration = $data['etebar'] ?? null;
        $scriptType->employee_status_id = $data['employeeStatusID'] ?? $scriptType->employee_status_id;
        $scriptType->isHeadable = $data['isHeadable'] ?? false;

        $scriptType->save();

        $confirmationTypeScriptType = json_decode($data['confirmationTypes'], true);
        $preparedData = $this->confirmationScriptDataPreparation($scriptType, $confirmationTypeScriptType);

        ConfirmationTypeScriptType::upsert($preparedData->toArray(), ['id']);
        $scriptType->load('employeeStatus', 'confirmationTypes');
        return $scriptType;
    }

    public function deleteScriptType(ScriptType $scriptType)
    {
        $scriptType->status_id = $this->inactiveScriptTypeScript()->id;
        $scriptType->save();
        return $scriptType;
    }

    public function inactiveScriptTypeScript()
    {
        return Cache::rememberForever('script_type_inactive_status', function () {
            return ScriptType::GetAllStatuses()
                ->firstWhere('name', $this->inactiveScriptTypeStatus);
        });
    }
}
