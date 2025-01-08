<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\SettingsMS\app\Models\Setting;

class ShowExamQuestionResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        $questionTimeSetting = Setting::where('key', 'time_per_questions')->first();
        $examNumberSetting = Setting::where('key', 'question_numbers_perExam')->first();

        $questionTime = $questionTimeSetting ? $questionTimeSetting->value : 0;
        $examNumber = $examNumberSetting ? $examNumberSetting->value : 0;

        $totalTime = $questionTime * $examNumber;

        $grouped = $this->collection->groupBy('questionID');

        $questions = $grouped->map(function ($items, $questionID) use ($questionTime) {
            $firstItem = $items->first();

            return [
                'question_id' => $questionID,
                'question_title' => $firstItem->questionTitle,
                'time_per_question' => $questionTime,
                'options' => $items->map(function ($item) {
                    return [
                        'option_id' => $item->optionID,
                        'option_title' => $item->optionTitle,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        return [
            'totalTime' => convertSecondToMinute($totalTime),
            'examQuestions' => $questions,
        ];
    }
}
