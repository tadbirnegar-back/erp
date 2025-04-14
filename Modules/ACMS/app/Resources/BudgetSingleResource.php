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
            'budgetID' => $this->id,
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
                'name' => $this->circularFile?->name,
                'slug' => $this->circularFile?->slug,
                'size' => $this->circularFile?->size,
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
                "totalIncome" => round($this->income_sum->jari_income_total + $this->income_sum->operational_income_total),
                "totalExpense" => round($this->eco_sum->economic_total + $this->operational_sum->operational_total),
                "difference" => round(($this->income_sum->jari_income_total + $this->income_sum->operational_income_total) - ($this->eco_sum->economic_total + $this->operational_sum->operational_total)),
                "currentIncome" => round($this->income_sum->jari_income_total),
                "currentExpense" => round($this->eco_sum->economic_total),
                "currentDifference" => round($this->income_sum->jari_income_total - $this->eco_sum->economic_total),
                "infrastructureIncome" => round($this->income_sum->operational_income_total),
                "infrastructureExpense" => round($this->operational_sum->operational_total),
                "infrastructureDifference" => round($this->income_sum->operational_income_total - $this->operational_sum->operational_total),
            ],
            'archive' => $this->ancestors->isEmpty() ? [] : $this->ancestors->map(function ($ancestor) {

                return [
                    'id' => $ancestor->id,
                    'name' => $ancestor->name,
                    'budgetType' => $ancestor->isSupplementary == 0 ? "بودجه اصلی" : "بودجه متمم",
                    'createDate' => convertGregorianToJalali($ancestor->create_date) ?? null,
                    'status' => [
                        'name' => $ancestor->latestStatus->name,
                        'class_name' => $ancestor->latestStatus->class_name
                    ],
                    'history' => BudgetTimelineStatusEnum::generateTimeline($ancestor)
                ];
            }),
            'head' => $this->ounitHead?->display_name,
            'financialManager' => $this->financialManager?->display_name,
        ];
    }
}
