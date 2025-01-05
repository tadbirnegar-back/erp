<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class LessonDataForupdateResource extends JsonResource
{
    public function toArray($request)
    {
        $lessonDetailsData = collect($this->resource['lessonDetails'])->groupBy('lessonData')->map(function ($lesson) {
            return [
                'id' => $lesson->first()->activeLesson,
                'title' => $lesson->first()->lesson_title,
                'course_id' => $lesson->first()->course_alias_id,
                'description' => $lesson->first()->lesson_description,
                'chapter' => [
                    'id' => $lesson->first()->chapter_alias_id,
                    'title' => $lesson->first()->chapter_alias_title,
                ],
                'extraChapters' => $lesson->map(function ($extraChapter) {
                    return[
                        'id' => $extraChapter->chapters_alias_id,
                        'title' => $extraChapter->chapters_alias_title,
                    ];
                })->filter()->unique('id')->values(),
                'contents' => $lesson->groupBy('content_id')->map(function ($content) {
                    return [
                        'id' => $content->first()->content_id,
                        'title' => $content->first()->content_title,
                        'content_type' => [
                            'type' => $content->first()->content_type_name,
                            'type_id' => $content->first()->content_type_id,
                        ],
                        'teacher' =>[
                            'teacher' => $content->first()->teacher_name,
                            'teacher_id' => $content->first()->teacher_alias_id,
                        ],
                    ];
                })->values(),
                'files' => $lesson->map(function ($file) {
                    $sizeWithUnit = Number::fileSize($file->lesson_file_size, 2, 3);
                    $parts = explode(' ', $sizeWithUnit, 2);
                    return [
                        'id' => $file->lesson_file_id,
                        'file_title' => $file->lesson_file_name,
                        'size' => intval(Number::fileSize($file->lesson_file_size, 2, 3)),
                        'Measurement_criteria' => $parts[1],
                    ];
                })->filter()->unique('id')->values(),
            ];
        })->values();

        $activeContent = collect($this->resource['lessonDetails'])
            ->filter(function ($content) {
                return !empty($content->content_consume_data);
            })
            ->sortByDesc(function ($content) {
                return $content->content_consume_create_date;
            })
            ->first();

        return [
            'lesson_details' => $lessonDetailsData,
        ];
    }
}
