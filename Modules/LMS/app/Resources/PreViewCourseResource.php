<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PreViewCourseResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->course_id,
            'title' => $this->examTitle,
            'QuestionType' => [
                ''
            ]

        ];

    }
}
