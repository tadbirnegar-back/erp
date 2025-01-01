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
            'id' => $this->id,
            'title' => $this->title,

            'course' => $this->courseTitle ? [
                'title' => $this->courseTitle,
            ] : null,

            'answer_sheet' => [
                'start_date_time' => $this->start_date_time ? convertGregorianToJalali($this->start_date_time) : null,
                'finish_date_time' => $this->finish_date_time ? convertGregorianToJalali($this->finish_date_time) : null,
            ],

            'counts' => [
                'questions' => $this->totalQuestions ?? 0,
            ],

        ];

    }
}
