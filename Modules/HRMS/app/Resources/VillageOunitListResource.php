<?php

namespace Modules\HRMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VillageOunitListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $res= [
            'id' => $this->id,
            'name' => $this->name,
            'ancestors' => $this->ancestors->pluck('name'),
        ];

        if (isset($this->abadi_code)) {
            $res['abadi_code']= $this->abadi_code;

        }

        return $res;
    }
}
