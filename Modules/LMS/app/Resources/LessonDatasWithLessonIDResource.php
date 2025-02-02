<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class LessonDatasWithLessonIDResource extends JsonResource
{
    public function toArray($request)
    {
        $lessonDetailsData = collect($this->resource['lessonDetails'])->groupBy('lessonData')->map(function ($lesson) {
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
                        'log' => $content->first()->content_consume_data
                            ? [
                                'set' => json_decode($content->first()->content_consume_set),
                                'last_played' => $content->first()->content_consume_last_played,
                                'consumation' => $content->first()->content_consume_data,
                                'create_date' => convertDateTimeGregorianToJalaliDateTime($content->first()->content_consume_create_date),
                            ]
                            : [],
                    ];
                })->values(),
                'comments' => $lesson->groupBy('commented_person_name')->map(function ($comment) {
                    $filteredComments = $comment->filter(function ($commentItem) {
                        return $commentItem->lesson_comment_text !== null;
                    });

                    $uniqueComments = $filteredComments->unique('lesson_comment_id');


                    return $uniqueComments->map(function ($validComment) {
                        return [
                            'id' => $validComment->lesson_comment_id,
                            'text' => $validComment->lesson_comment_text,
                            'created_at' => $validComment->lesson_comment_create_date
                                ? convertDateTimeGregorianToJalaliDateTime($validComment->lesson_comment_create_date)
                                : null,
                            'commented_person' => $validComment->commented_person_name,
                            'avatar' => url($validComment->commented_person_avatar),
                        ];
                    });
                })->filter()->values()->flatten(1)->first(),

                'files' => $lesson->map(function ($file) {
                    $sizeWithUnit = Number::fileSize($file->lesson_file_size, 2, 3);
                    $parts = explode(' ', $sizeWithUnit, 2);
                    return [
                        'id' => $file->lesson_file_id,
                        'file_title' => $file -> lesson_file_title,
                        'url' => url($file->lesson_file_slug),
                        'size' => intval(Number::fileSize($file->lesson_file_size, 2, 3)) ,
                        'Measurement_criteria' => $parts[1],
                    ];
                })->filter()->unique('id')->values(),
            ];
        })->values();

        $activeContent = collect($this->resource['lessonDetails'])
//            ->filter(function ($content) {
//                return !empty($content->content_consume_data);
//            })
            ->sortByDesc(function ($content) {
                return $content->content_consume_create_date;
            })
            ->first();

        return [
            'lesson_details' => $lessonDetailsData->first(),
            'activeContent' => $activeContent ? $activeContent->content_id : null,
            'user' => $this -> getUserData()
        ];
    }

    private function getUserData()
    {
        $user = \Auth::user();
        return $user->load('person.avatar');
    }
}
