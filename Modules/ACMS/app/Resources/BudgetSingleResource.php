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
            'createDate' => convertDateTimeGregorianToJalaliDateTime($this->create_date) ?? null,
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
            'status' => ['name' => $this->latestStatus->name,
                'class_name' => $this->latestStatus->class_name],
            'history' => BudgetTimelineStatusEnum::generateTimeline($this),
            'generalStatistic' => [
                "totalIncome" => 128000000,
                "totalExpense" => 128000000,
                "difference" => 0,
                "total" => 128000000,
                "currentIncome" => 120000000,
                "currentExpense" => 128000000,
                "currentDifference" => 0,
                "infrastructureIncome" => 128000000,
                "infrastructureExpense" => 128000000,
                "infrastructureDifference" => 0,
            ]
        ];
    }
}
