<?php

namespace Modules\ACMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VillageBudgetListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'name' => $this->ounit_name ?? $this->budget_name,
            'fiscalYear' => $this->fiscalYear?->name,
            'status' => [
                'name' => $this->status_name,
                'class_name' => $this->status_class_name,
            ],
            'budget_id' => $this->budget_id,
            'village_abadiCode' => $this->village_abadicode,
        ];
    }
}
