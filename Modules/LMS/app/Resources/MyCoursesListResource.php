<?php
namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Models\Lesson;

class MyCoursesListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        $data = collect($this->resource);

        $groupedData = $data->groupBy('course_alias_id')->map(function ($courseItems) {
            $contentTypes = $courseItems->pluck('content_type_alias_name')->unique()->values();

            $lessons = $courseItems->groupBy('lesson_id')->values()->map(function ($lessonItems) {
                $lesson = $lessonItems->first();
                $status = optional($this->checkStatusOfLesson($lesson['lesson_id']))->latestStatus[0]->name ?? null;

                // Attach status to lesson data
                $lesson['status_name'] = $status;
                return $lesson;
            });

            // Filter out inactive lessons
            $activeLessons = $lessons->filter(function ($lesson) {
                return $lesson['status_name'] === LessonStatusEnum::ACTIVE->value;
            });

            // Calculate completed lessons percentage
            $totalLessons = $activeLessons->count();
            $completedLessons = $activeLessons->where('lesson_is_completed', true)->count();
            $completionPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0;

            return [
                'course_alias_id' => $courseItems->first()['course_alias_id'],
                'avatar_alias_slug' => url($courseItems->first()['avatar_alias_slug']),
                'course_alias_title' => $courseItems->first()['course_alias_title'],
                'content_types' => $contentTypes,
                'lesson_count' => $totalLessons, // Only count active lessons
                'completed_percentage' => $completionPercentage,
            ];
        });

        return [
            'data' => $groupedData->values(),
        ];
    }

    private function checkStatusOfLesson($lessonId)
    {
        return Lesson::with('latestStatus')->find($lessonId);
    }
}
