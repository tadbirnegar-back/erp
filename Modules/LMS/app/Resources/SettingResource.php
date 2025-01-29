<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return array_filter([
            'key' => $this->key,
            'value' => $this->value,
            'questionType' => $this->questionType,
            'difficulty' => $this->difficulty,
        ], function ($value) {
            return !is_null($value);
        });
    }
}
