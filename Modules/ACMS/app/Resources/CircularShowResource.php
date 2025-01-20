<?php

namespace Modules\ACMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Modules\ACMS\app\Http\Enums\BudgetStatusEnum;
use Modules\ACMS\app\Http\Enums\SubjectTypeEnum;

class CircularShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $result = [
            'id' => $this->id,
            'name' => $this->name,
            'file' => [
                'id' => $this->file_id,
                'slug' => url('/') . '/' . $this->file_slug,
                'name' => $this->file_name,
                'size' => Number::fileSize($this->file_size),
            ],
            'fiscalYear' => [
                'name' => $this->fiscal_year_name,
                'id' => $this->fiscal_year_id,
            ],
            'subjects' => !is_null($this?->subjects) ? $this->subjectsGenerator($this->subjects) : [],
            'status' => isset($this->status_name) ? [
                'name' => $this->status_name,
                'class_name' => $this->status_class_name
            ] : null,
            'create_date' => isset($this->create_date) ? DateformatToHumanReadableJalali(convertGregorianToJalali($this->create_date), false) : null,
            'dispatchedUnits' => $this->dispatchedOunits,
            'unDispatchedUnits' => $this->unDispatchedOunits,
            'budgetsCountByStatus' => $this->budgetsCounterByStatus($this->budgetCounts),

        ];

        return $result;
    }

    public function subjectsGenerator($subjects)
    {
        $subjectTypes = SubjectTypeEnum::cases();

        $result = [];
        foreach ($subjectTypes as $subjectType) {
            $item = $subjects->firstWhere('subject_type_id', $subjectType->value);

            if ($item) {
                $result[$subjectType->name] = [
                    'type' => $subjectType->getLabelAndValue(),
                    'count' => $item->count,
                ];
            } else {
                $result[$subjectType->name] = [
                    'type' => $subjectType->getLabelAndValue(),
                    'count' => 0,
                ];
            }
        }

        return $result;

    }

    public function budgetsCounterByStatus($budgets)
    {
        $statuses = BudgetStatusEnum::cases();

        $result = [];
        foreach ($statuses as $status) {
            if ($status->value != BudgetStatusEnum::CANCELED->value) {
                $item = $budgets->firstWhere('status_name', $status->value);
                if ($item) {
                    $result[$status->name] = [
                        'status_name' => $item->status_name,
                        'status_class_name' => $item->status_class_name,
                        'count' => $item->count,
                    ];
                } else {
                    $result[$status->name] = [
                        'status_name' => $status->value,
                        'status_class_name' => '',
                        'count' => 0,
                    ];
                }
            }
        }

        return $result;
    }
}
