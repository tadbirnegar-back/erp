<?php

namespace Modules\ACMS\app\Http\Trait;

use Modules\AAA\app\Models\User;
use Modules\ACMS\app\Http\Enums\AccountantScriptTypeEnum;
use Modules\ACMS\app\Http\Enums\BudgetStatusEnum;
use Modules\ACMS\app\Models\Budget;
use Modules\ACMS\app\Models\BudgetStatus;
use Modules\ACMS\app\Models\Circular;
use Modules\ACMS\app\Models\OunitFiscalYear;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;
use Modules\StatusMS\app\Models\Status;

trait BudgetTrait
{

    public function bulkStoreBudget(array $data, string $name, User $user, Circular $circular)
    {
        $preparedData = $this->budgetDataPreparation($data, $name, $circular);

        $budgets = Budget::insert($preparedData->toArray());

        $budgetResult = Budget::latest('id')
            ->take($preparedData->count())
            ->get(['id']);

        $status = $this->proposedBudgetStatus();

        $preparedDataBudgetStatus = $this->budgetStatusDataPreparation($budgetResult->toArray(), $status, $user);

        $budgetStatuses = BudgetStatus::insert($preparedDataBudgetStatus->toArray());

        return $budgetResult;

    }

    public function budgetDataPreparation(array $data, string $name, Circular $circular)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) use ($name, $circular) {
            $ounitFiscalYear = OunitFiscalYear::with('ounit.head')->find($item['id']);

            $scriptType = ScriptType::where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value)->first();

            $financialManager = RecruitmentScript::join('recruitment_script_status as rss', 'recruitment_scripts.id', '=', 'rss.recruitment_script_id')
                ->join('statuses as s', 'rss.status_id', '=', 's.id')
                ->where('s.name', 'فعال')
                ->where('rss.create_date', function ($subQuery) {

                    $subQuery->selectRaw('MAX(create_date)')
                        ->from('recruitment_script_status as sub_rss')
                        ->whereColumn('sub_rss.recruitment_script_id', 'rss.recruitment_script_id');
                })
                ->where('script_type_id', $scriptType->id)
                ->where('organization_unit_id', $ounitFiscalYear->ounit->id)
                ->with(['user'])
                ->first();
            return [
                'name' => $name,
                'isSupplementary' => $item['isSupplementary'] ?? false,
                'ounitFiscalYear_id' => $item['id'],
                'parent_id' => $item['parentID'] ?? null,
                'circular_id' => $circular?->id,
                'financial_manager_id' => $financialManager->user->id ?? null,
                'ounit_head_id' => $ounitFiscalYear->ounit?->head?->id ?? null,
            ];
        });

        return $data;

    }

    public function budgetStatusDataPreparation(array $data, Status $status, User $user)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) use ($status, $user) {
            return [
                'budget_id' => $item['id'],
                'status_id' => $status->id,
                'creator_id' => $user->id,
                'create_date' => now(),
            ];
        });
        return $data;
    }

    public function proposedBudgetStatus()
    {
        return Budget::GetAllStatuses()->firstWhere('name', BudgetStatusEnum::PROPOSED->value);
    }

    public function finalizedBudgetStatus()
    {
        return Budget::GetAllStatuses()->firstWhere('name', BudgetStatusEnum::FINALIZED->value);
    }
}
