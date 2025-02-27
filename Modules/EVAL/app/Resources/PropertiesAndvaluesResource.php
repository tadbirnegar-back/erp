<?php

namespace Modules\EVAL\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertiesAndvaluesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'value' => $this->id,
            'label' => $this->value ? "بله" : "خیر",
            'value' => $this->id,
            'label' => $this->name,
        ];
    }
}
