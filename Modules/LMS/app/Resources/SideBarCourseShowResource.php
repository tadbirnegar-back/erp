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
        $activeLessonID = $this->resource['lessonID'];

        // Find the chapter of the active lesson
        $activeLesson = collect($this->resource['sidebar'])->firstWhere('lesson_id', $activeLessonID);
        $activeChapterID = $activeLesson ? $activeLesson->chapter_id : null;

        $sidebarData = collect($this->resource['sidebar'])->groupBy('chapter_id')->map(function ($items, $chapterId) use ($activeLessonID, $activeChapterID) {
            return [
                'chapter_name' => $items->first()->chapter_title,
                'chapter_id' => $chapterId,
                'lessons' => $items->groupBy(function ($lessonItem) {
                    return $lessonItem->lesson_id . '-' . $lessonItem->chapter_id;
                })->map(function ($lessonGroup) use ($activeLessonID, $activeChapterID, $chapterId) {
                    return $lessonGroup->map(function ($lessonItem) use ($activeLessonID, $activeChapterID, $chapterId) {
                        $durations = is_array($lessonItem->files_duration) ? $lessonItem->files_duration : [convertSecondToMinute($lessonItem->files_duration)];

                        return [
                            'lesson_title' => $lessonItem->lesson_title,
                            'is_completed' => $lessonItem->is_completed,
                            'durations' => $durations,
                            'lesson_id' => $lessonItem->lesson_id,
                            'chapter_id' => $lessonItem->chapter_id,
                            'canShow' => ($lessonItem->lesson_id <= $activeLessonID) && ($chapterId <= $activeChapterID), // Ensuring correct chapter
                        ];
                    })->reduce(function ($carry, $lessonItem) {
                        $carry['durations'] = array_unique(array_merge($carry['durations'] ?? [], $lessonItem['durations']));
                        if (!isset($carry['lesson_title'])) {
                            $carry['lesson_title'] = $lessonItem['lesson_title'];
                        }
                        if (!isset($carry['is_completed'])) {
                            $carry['is_completed'] = $lessonItem['is_completed'];
                        }
                        if (!isset($carry['canShow'])) {
                            $carry['canShow'] = $lessonItem['canShow'];
                        }
                        $carry['lesson_id'] = $lessonItem['lesson_id'];
                        return $carry;
                    }, []);
                })->values()->all(),
            ];
        })->values()->all();

        return [
            'sidebar' => $sidebarData,
            'activeLessonID' => $activeLessonID,
        ];
    }
}
