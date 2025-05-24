<?php

namespace Modules\SMM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OunitEmployeesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $employees = collect(json_decode($this->employees));
        return [
            'ounitID' => $this->organization_unit_id,
            'ounitName' => $this->organization_unit_name,
            'employees' => StaffDetailListResource::collection($employees),
        ];
    }
}
