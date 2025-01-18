<?php

namespace Modules\LMS\app\Resources;

use Exception;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultResource extends JsonResource
{
    public function toArray($request)
    {
        $data = $this->resource;

        if (!isset($data['answerSheet'])) {
            throw new Exception('Invalid data structure: "answerSheet" key is missing.');
        }

        $usedTime = $data['usedTime'] ?? null;
        $calculate = $data['calculate'] ?? [];
        $studentInfo = $data['studentInfo'] ?? [];
        $status = $data['status'];
        $userAns = $data['userAnswer'];

        // فیلتر کردن پاسخ‌های صحیح
        $filteredAnswers = collect($data['answerSheet'])->filter(function ($sheet) {
            return $sheet->isCorrect ?? false; // فقط پاسخ‌های صحیح
        });

        // تبدیل داده‌های فیلتر شده
        $transformed = $filteredAnswers->map(function ($sheet) {
            return [
                'id' => $sheet->answerSheetID ?? null,
                'questionsAndOptions' => [
                    'question_id' => $sheet->questionID ?? null,
                    'question_title' => $sheet->questionTitle ?? null,
                    'option_id' => $sheet->optionID ?? null,
                    'option_title' => $sheet->optionTitle ?? null,
                    'is_correct' => $sheet->isCorrect ?? null,
                ],
            ];
        });

        return [
            'status' => $status,
            'student' => $studentInfo,
            'correctAnswers' => $transformed->values(), // نمایش پاسخ‌های صحیح
            'calculate' => $calculate,
            'userAnswer' => $userAns,
        ];
    }
}
