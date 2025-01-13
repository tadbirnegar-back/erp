<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\SettingsMS\app\Models\Setting;

class ExamPreviewResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        // Retrieve settings
        $questionTimeSetting = Setting::where('key', 'time_per_questions')->first();
        $examNumberSetting = Setting::where('key', 'question_numbers_perExam')->first();

        $questionTime = $questionTimeSetting ? $questionTimeSetting->value : 0;
        $examNumber = $examNumberSetting ? $examNumberSetting->value : 0;

        // Group by exam ID
        $grouped = $this->collection->groupBy('id');

        return $grouped->map(function ($items, $id) use ($questionTime, $examNumber) {
            $firstItem = $items->first();
            $totalQuestions = $items->count(); // Total questions
            $examTime = $questionTime * $examNumber;

            return [
                'exam_id' => $id,
                'exam_title' => $firstItem->examTitle ?? null,
                'questionsCount' => $totalQuestions,
                'course_title' => $firstItem->courseTitle ?? null,
                'timePerQuestion' => convertSecondToMinute($questionTime),
                'exam_time' => convertSecondToMinute($examTime),
            ];
        })->values()->toArray();
    }
}
