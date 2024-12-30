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
        $sidebarData = collect($this->resource['sidebar'])->groupBy('chapter_id')->map(function ($items, $chapterId) {
            return [
                'chapter_name' => $items->first()->chapter_title,
                'chapter_id' => $chapterId,
                'lessons' => $items->groupBy(function ($lessonItem) {
                    return $lessonItem->lesson_id . '-' . $lessonItem->chapter_id;
                })->map(function ($lessonGroup) {
                    return $lessonGroup->map(function ($lessonItem) {
                        $durations = is_array($lessonItem->files_duration) ? $lessonItem->files_duration : [convertSecondToMinute($lessonItem->files_duration)];

                        return [
                            'lesson_title' => $lessonItem->lesson_title,
                            'is_completed' => $lessonItem->is_completed,
                            'durations' => $durations,
                            'lesson_id' => $lessonItem->lesson_id,
                            'chapter_id' => $lessonItem->chapter_id,
                        ];
                    })->reduce(function ($carry, $lessonItem) {
                        $carry['durations'] = array_unique(array_merge($carry['durations'] ?? [], $lessonItem['durations']));
                        if (!isset($carry['lesson_title'])) {
                            $carry['lesson_title'] = $lessonItem['lesson_title'];
                        }
                        if (!isset($carry['is_completed'])) {
                            $carry['is_completed'] = $lessonItem['is_completed'];
                        }
                        $carry['lesson_id'] = $lessonItem['lesson_id'];
                        return $carry;
                    }, []);
                })->values()->all(),
            ];
        })->values()->all();

        $lessonDetailsData = $this->resource['lessonID'];

        return [
            'sidebar' => $sidebarData,
            'activeLessonID' => $lessonDetailsData,
        ];
    }
}
