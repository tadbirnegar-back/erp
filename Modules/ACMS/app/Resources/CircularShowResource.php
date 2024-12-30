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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => [
                'name' => $this->latestStatus->name,
                'class_name' => $this->latestStatus->class_name
            ],
            'file' => $this->file->slug,
            'subjects' => $this->circularSubjects->toHierarchy(),
        ];
    }
}
