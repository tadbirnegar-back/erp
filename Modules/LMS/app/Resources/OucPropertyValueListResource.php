<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OucPropertyValueListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'value' => $this->id,
            'label' => $this->value ? "بله" : "خیر",
        ];
    }
}
