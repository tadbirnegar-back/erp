<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamPreviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'exam' => $this->examTitle ? [
                'title' => $this->examTitle,
            ] : null,

            'course' => $this->courseTitle ? [
                'title' => $this->courseTitle,
            ] : null,

            'answer_sheet' => [
                'start_date_time' => $this->start_date_time ? convertGregorianToJalali($this->start_date_time) : null,
                'finish_date_time' => $this->finish_date_time ? convertGregorianToJalali($this->finish_date_time) : null,
            ],

            'counts' => [
                'questions' => $this->totalQuestions ?? 0,
                'chapters' => $this->chapters_count ?? 0,
            ],

            'chapter' => $this->chapter_title ? [
                'title' => $this->chapter_title,

                'lesson' => $this->lesson_title ? [
                    'title' => $this->lesson_title,
                ] : null,
            ] : null,
        ];

    }
}
