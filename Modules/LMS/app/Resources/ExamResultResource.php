<?php

namespace Modules\LMS\app\Resources;

use Exception;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultResource extends JsonResource
{
    protected string $baseUrl;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->baseUrl = rtrim(url('/'), '/'); // Initialize base URL and ensure no trailing slash
    }

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
        $startTime = $data['startDate'];

        $jalaliStartDate = $startTime ? convertDateTimeGregorianToJalaliDateTime($startTime) : null;


        $studentInfo['avatar'] = isset($studentInfo['avatar']) && $studentInfo['avatar']
            ? $this->baseUrl . '/' . ltrim($studentInfo['avatar'], '/')
            : "{$this->baseUrl}/default-avatar.png";

        $filteredAnswers = collect($data['answerSheet'])->filter(function ($sheet) {
            return $sheet->isCorrect ?? false;
        });

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
            'correctAnswers' => $transformed->values(),
            'calculate' => $calculate,
            'startDateTime' => $jalaliStartDate,
            'userAnswer' => $userAns,
        ];
    }
}
