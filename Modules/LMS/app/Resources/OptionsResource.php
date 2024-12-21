<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OptionsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'isCorrect' => $this->is_correct,
            'questionID' => $this->question_id,
            'title' => $this->title,
        ];
    }


}
