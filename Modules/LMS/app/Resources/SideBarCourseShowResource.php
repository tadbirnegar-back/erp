<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SideBarCourseShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        // Grouping the sidebar data by 'sidebar' key
        $sidebarData = collect($this->resource['sidebar'])->groupBy('sidebar')->map(function ($item) {
            // Process sidebar items here, such as chapter and lessons information
            return [
                'title' => $item->first()->chapter_title,
                'description' => $item->first()->chapter_description,
                'lessons' => $item->groupBy('lesson_id')->map(function ($lesson) {
                    return [
                        'id' => $lesson->first()->lesson_id,
                        'title' => $lesson->first()->lesson_title,
                        'isComplete' => $lesson->first()->is_completed,
                        'duration' => convertSecondToMinute($lesson->first()->files_duration),
                        'chapter_id' => $lesson->first()->chapter_id,
                    ];
                })->values(),
            ];
        })->values();

        $lessonDetailsData = $this->resource['lessonID'];
        return [
            'sidebar' => $sidebarData,
            'lesson_details' => $lessonDetailsData,
        ];
    }


}
