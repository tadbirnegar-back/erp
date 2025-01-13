<?php
namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyCoursesListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        $data = collect($this->resource); // Ensure it's a collection

        // Group by `course_alias_id`
        $groupedData = $data->groupBy('course_alias_id')->map(function ($courseItems) {
            // Extract distinct content types for the course
            $contentTypes = $courseItems->pluck('content_type_alias_name')->unique()->values();

            // Group lessons by `lesson_id` within each course
            $lessons = $courseItems->groupBy('lesson_id')->values()->map(function ($lessonItems) {
                return $lessonItems->values()->first(); // Re-index each lesson group
            });

            // Calculate completed lessons percentage
            $totalLessons = $lessons->count();
            $completedLessons = $lessons->filter(function ($lesson) {
                return $lesson['lesson_is_completed'] == true;
            })->count();
            $completionPercentage = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;

            return [
                'course_alias_id' => $courseItems->first()['course_alias_id'],
                'avatar_alias_slug' => url($courseItems->first()['avatar_alias_slug']),

                'course_alias_title' => $courseItems->first()['course_alias_title'],
                'content_types' => $contentTypes, // Add distinct content types
                'lesson_count' => $totalLessons, // Add total lesson count
                'completed_percentage' => round($completionPercentage, 2), // Add completion percentage
            ];
        });

        return [
            'data' => $groupedData->values(),
        ];
    }
}
