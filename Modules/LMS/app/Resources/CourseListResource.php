<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseListResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'cover' => [
                'slug' => url($this->cover_slug),
            ],
            'statuses' => [[
                "name" => $this->status_name,
                "class_name" => $this->status_class_name,
            ]],
            'counts' => [
                'chapters' => $this->chapters_count,
                'lessons' => $this->all_active_lessons_count,
                'questions' => $this->questions_count,
            ]
        ];
    }
}
