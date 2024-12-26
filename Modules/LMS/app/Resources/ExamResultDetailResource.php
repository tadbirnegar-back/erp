<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ExamResultDetailResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        $groupedResults = $this->collection->groupBy('id');

        return [
            'data' => $groupedResults->map(function ($group) {
                return $group->unique('exam_id')->map(function ($item) {
                    return [
                        'exam_id' => $item->exam_id,
                        'student_id' => $item->student_id,
                        'score' => $item->score,
                        'status' => [
                            'status_name' => $item->status->name,
                            'class_name' => $item->status->class_name,
                        ],
                        'questions' => [
                            'question_title' => $item->question_title,
                        ],
                        'options' => [
                            'option_title' => $item->option_title,
                        ],
                        'answer_sheet' => [
                            'start_date_time' => $item->start_date_time,
                            'finish_date_time' => $item->finish_date_time,
                            'used_time_in_minutes' => $item->used_time_in_minutes,
                        ],
                        'answers' => [
                            'null_answers_count' => $item->null_answers_count,
                            'correct_answers_count' => $item->correct_answers_count,
                            'questions_count' => $item->questions_count,
                            'false_answers_count' => $item->false_answers_count,
                        ]
                    ];
                });
            }),
        ];
    }
}
