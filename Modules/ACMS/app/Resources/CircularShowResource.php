<?php

namespace Modules\ACMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
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
            'subjects' => $this->relationLoaded('circularSubjects') ? $this->subjectsGenerator($this->circularSubjects) : [],
            'status' => isset($this->status_name) ? [
                'name' => $this->status_name,
                'class_name' => $this->status_class_name
            ] : null,
        ];

        return $result;
    }

    public function subjectsGenerator($subjects)
    {
        $grouped = $subjects->groupBy('subject_type_id');
        $subjectTypes = SubjectTypeEnum::cases();

        $result = [];
        foreach ($subjectTypes as $subjectType) {
            if (isset($grouped[$subjectType->value])) {
                $result[$subjectType->name] = [
                    'type' => $subjectType->getLabel(),
                    'items' => $grouped[$subjectType->value]->toHierarchy(),
                    'count' => $grouped[$subjectType->value]->count(),
                ];
            } else {
                $result[$subjectType->name] = [
                    'type' => $subjectType->getLabel(),
                    'items' => [],
                    'count' => 0,
                ];
            }
        }

        return $result;

    }
}
