<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Models\Lesson;

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

        // Filter only active lessons
        $filteredSidebar = collect($this->resource['sidebar'])->filter(function ($lessonItem) {
            return $this->isLessonActive($lessonItem->lesson_id);
        });

        $sidebarData = $filteredSidebar->groupBy('chapter_id')->map(function ($items, $chapterId) use ($activeLessonID, $activeChapterID) {
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
                            'canShow' => $lessonItem->is_completed || (($lessonItem->lesson_id <= $activeLessonID) && ($chapterId <= $activeChapterID)),
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

    /**
     * Check if the lesson is active
     */
    private function isLessonActive($lessonId)
    {
        $lesson = Lesson::with('latestStatus')->find($lessonId);

        return optional($lesson->latestStatus->first())->name === LessonStatusEnum::ACTIVE->value;
    }

}
