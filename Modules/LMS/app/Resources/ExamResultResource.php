<?php

namespace Modules\LMS\app\Resources;

use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\SettingsMS\app\Models\Setting;

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
        $questionTimeSetting = Setting::where('key', 'time_per_questions')->first();
        $examNumberSetting = Setting::where('key', 'question_numbers_perExam')->first();

        $questionTime = $questionTimeSetting ? $questionTimeSetting->value : 0;
        $examNumber = $examNumberSetting ? $examNumberSetting->value : 0;
        $examTime = $questionTime * $examNumber;

        $jalaliStartDate = $startTime ? convertDateTimeGregorianToJalaliDateTime($startTime) : null;

        $studentInfo['avatar'] = isset($studentInfo['avatar']) && $studentInfo['avatar']
            ? $this->baseUrl . '/' . ltrim($studentInfo['avatar'], '/')
            : "{$this->baseUrl}/default-avatar.png";

        $groupedAnswers = collect($data['answerSheet'])
            ->groupBy('questionID')
            ->map(function ($answers, $questionID) {
                return [
                    'question_id' => $questionID,
                    'question_title' => $answers->first()->questionTitle ?? null,
                    'correctAnswers' => $answers->filter(function ($answer) {
                        return ($answer->isCorrect ?? false) == 1;
                    })->map(function ($answer) {
                        return [
                            'option_id' => $answer->optionID ?? null,
                            'option_title' => $answer->optionTitle ?? null,
                        ];
                    })->first(),
                    'AllAnswers' => $answers->map(function ($answer) {
                        return [
                            'option_id' => $answer->optionID ?? null,
                            'option_title' => $answer->optionTitle ?? null,
                        ];
                    })->values(),
                ];
            })->values();


        return [
            'status' => $status,
            'student' => $studentInfo,
            'answers' => $groupedAnswers,
            'calculate' => $calculate,
            'startDateTime' => $jalaliStartDate,
            'userAnswer' => $userAns,
            'exam_time' => $examTime,
        ];
    }
}
