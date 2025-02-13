<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'course' => [
                'courseTitle' => $this->courseTitle,
                'chapters_count' => $this->chaptersCount,
                'all_active_lessons_count' => $this->allActiveLessonsCount,
            ],
        ];
    }
}
