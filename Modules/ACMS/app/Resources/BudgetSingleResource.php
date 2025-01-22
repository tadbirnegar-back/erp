<?php

namespace Modules\ACMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ACMS\app\Http\Enums\BudgetTimelineStatusEnum;

class BudgetSingleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'createDate' => convertGregorianToJalali($this->create_date) ?? null,
            'creator' => $this->statuses[0]->pivot->person->display_name,
            'budgetType' => $this->isSupplementary == 0 ? "بودجه اصلی" : "بودجه متمم",
            'budgetEdition' => $this->ancestors->count() + 1,
            'fiscalYear' => ['name' => $this->fiscalYear->name],
            'ounit' => ['name' => $this->ounit,
                'abadi_code' => $this->village->abadi_code,
                'ancestors' => $this->ounit->ancestors->pluck('name')
            ],
            'file' => [
                'name' => $this->circularFile->name,
                'slug' => $this->circularFile->slug,
                'size' => $this->circularFile->size,
            ],
            'status' => [
                'name' => $this->latestStatus->name,
                'class_name' => $this->latestStatus->class_name
            ],
            'incomeItemsCount' => $this->income_sum->count,
            'economyItemsCount' => $this->eco_sum->count,
            'operationalItemCount' => $this->operational_sum->count,
            'history' => BudgetTimelineStatusEnum::generateTimeline($this),
            'generalStatistic' => [
                "totalIncome" => ($this->income_sum->jari_income_total + $this->income_sum->operational_income_total),
                "totalExpense" => ($this->eco_sum->economic_total + $this->operational_sum->operational_total),
                "difference" => (($this->income_sum->jari_income_total + $this->income_sum->operational_income_total) - ($this->eco_sum->economic_total + $this->operational_sum->operational_total)),
                "currentIncome" => $this->income_sum->jari_income_total,
                "currentExpense" => $this->eco_sum->economic_total,
                "currentDifference" => ($this->income_sum->jari_income_total - $this->eco_sum->economic_total),
                "infrastructureIncome" => $this->income_sum->operational_income_total,
                "infrastructureExpense" => $this->operational_sum->operational_total,
                "infrastructureDifference" => ($this->income_sum->operational_income_total - $this->operational_sum->operational_total),
            ],
            'archive' => $this->ancestors->isEmpty() ? [] : $this->ancestors->map(function ($ancestor) {

                return [
                    'name' => $ancestor->name,
                    'status' => [
                        'name' => $ancestor->latestStatus->name,
                        'class_name' => $ancestor->latestStatus->class_name
                    ],
                    'history' => BudgetTimelineStatusEnum::generateTimeline($ancestor)
                ];
            }),
        ];
    }
}
