<?php

namespace Modules\ACMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccounterOunitsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
            'abadi_code' => $this->village->abadi_code,
            'ancestors' => $this->ancestors->map(function ($item) {
                return ['name' => $item->name];
            }),
        ];
    }
}
