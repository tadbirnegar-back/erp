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

        $lessonDetailsData = collect($this->resource['lessonData'])->groupBy('lessonData')->map(function ($lesson) {
            return [
                'id' => $lesson->first()->activeLesson,
                'title' => $lesson->first()->lesson_title,
                'description' => $lesson->first()->lesson_description,
                'contents' => $lesson->groupBy('content_id')->map(function ($content) {
                    return [
                        'id' => $content->first()->content_id,
                        'title' => $content->first()->content_title,
                        'type' => $content->first()->content_type_name,
                        'file_url' => url($content->first()->content_file_alias),
                        'teacher' => $content->first()->teacher_name,
                        'teacher_avatar' => url($content->first()->teacher_avatar),
                        'teacher_id' => $content->first()->teacher_alias_id,
                        'comments' => $content->groupBy('commented_person_name')->map(function ($comment) {
                            return [
                                'text' => $comment->first()->lesson_comment_text,
                                'created_at' => $comment->first()->comment_created_at
                                    ? convertGregorianToJalali($comment->first()->comment_created_at)
                                    : null,
                                'commented_person' => $comment->first()->commented_person_name,
                                'avatar' => url($comment->first()->commented_person_avatar),
                            ];
                        })->values(),
                        'log' => [
                            'set' => $content->first()->content_consume_set,
                            'last_played' => $content->first()->content_consume_last_played,
                            'consumation' => $content->first()->content_consume_data
                        ]
                    ];
                })->values(),
                'files' => $lesson->map(function ($file) {
                    return [
                        'file_title' => $file->lesson_file_slug,
                        'url' => url($file->lesson_file_slug),
                    ];
                })->filter(),
            ];
        })->values();

        return [
            'sidebar' => $sidebarData,
            'lesson_details' => $lessonDetailsData,
        ];
    }


}
