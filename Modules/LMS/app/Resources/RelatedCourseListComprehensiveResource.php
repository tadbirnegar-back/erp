<?php

namespace Modules\LMS\app\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\LMS\app\Http\Enums\AnswerSheetStatusEnum;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Models\Course;

class RelatedCourseListComprehensiveResource extends JsonResource
{
    use CourseTrait;

    public function toArray($request): array
    {
        return [
            'course_id' => $this->id,
            'course_title' => $this->course_title,
            'course_exp_date' => $this->course_exp_date ? Carbon::parse($this->course_exp_date)->diffInDays(Carbon::now()) : null,
            'status_name' => $this->status_name ?? AnswerSheetStatusEnum::WAIT_TO_EXAM->value,
            'class_name' => $this->class_name ?? 'primary',
            'cover_slug' => url($this->cover_slug),
            'lesson_count' => $this->all_active_lessons_count,
            'distinct_content_types' => $this->contentTypes->pluck('name')->toArray(),
        ];
    }


}
