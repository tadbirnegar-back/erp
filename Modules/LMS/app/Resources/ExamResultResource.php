<?php

namespace Modules\LMS\app\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExamResultResource extends ResourceCollection
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

                    $startTime = Carbon::parse($item->start_date_time);
                    $finishTime = Carbon::parse($item->finish_date_time);
                    $usedTimeInMinutes = $startTime->diffInMinutes($finishTime);

                    return [
//                        'courseID' => $item->courseID,
                        'studentID' => $item->student_id,
                        'score' => $item->score,
                        'status' => [
                            'statusName' => $item->status->name,
                            'className' => $item->status->class_name,
                        ],
                        'questions' => [
                            'questionTitle' => $item->questionTitle,
                        ],
                        'options' => [
                            'optionTitle' => $item->optionTitle,
                        ],
                        'answer_sheet' => [
                            'startDateTime' => $item->startDateTime,
                            'finishDateTime' => $item->finishDateTime,
                            'usedTimeInMinutes' => $usedTimeInMinutes,
                        ],
                        'answers' => [
                            'nullAnswersCount' => $item->null_answers_count,
                            'correctAnswersCount' => $item->correct_answers_count,
                            'questionsCount' => $item->questions_count,
                            'falseAnswersCount' => $item->false_answers_count,
                            'trueAnswer' => $item->correct_answer,
                        ],
                    ];
                });
            }),
        ];
    }
}
