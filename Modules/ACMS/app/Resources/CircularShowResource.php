<?php

namespace Modules\ACMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
                'id' => $this->file->id,
                'slug' => $this->file->slug,
                'name' => $this->file->name,
                'size' => $this->file->size,
            ],
        ];

        if ($this->relationLoaded('fiscalYear')) {
            $result['fiscalYear'] = [
                'name' => $this->fiscalYear->name,
                'id' => $this->fiscalYear->id,
            ];
        }

        if ($this->relationLoaded('circularSubjects')) {
            $result['subjects'] = $this->circularSubjects->toHierarchy();
        }

        if ($this->relationLoaded('circularSubjects')) {
            $result['status'] = [
                'name' => $this->latestStatus->name,
                'class_name' => $this->latestStatus->class_name
            ];
        }
        return $result;
    }
}
