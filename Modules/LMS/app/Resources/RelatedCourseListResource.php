<?php

namespace Modules\LMS\app\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Models\Course;

class RelatedCourseListResource extends JsonResource
{
    use CourseTrait;

    public function toArray($request): array
    {
        return [
            'course_id' => $this->id,
            'course_title' => $this->course_title,
            'course_exp_date' => $this->course_exp_date,
            'status_name' => $this->status_name,
            'class_name' => $this->class_name,
            'cover_slug' => url($this->cover_slug),
            'lesson_count' => $this->lessons_count,
            'distinct_content_types' => $this->contentTypes->pluck('name')->toArray(),
        ];
    }


}
