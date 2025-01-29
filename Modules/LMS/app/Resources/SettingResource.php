<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */

    public function toArray($request)
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
            'questionType' => $this->when($this->key === 'question_type_for_exam', [
                'name' => $this->questionType ? $this->questionType : null,
                'value' => $this->value,
            ]),
            'difficulty' => $this->when($this->key === 'Difficulty_for_exam', [
                'name' => $this->difficulty ? $this->difficulty : null,
                'value' => $this->value,
            ]),
        ];
    }


}
