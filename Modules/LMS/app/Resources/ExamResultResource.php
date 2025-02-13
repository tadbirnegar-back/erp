<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\SettingsMS\app\Models\Setting;

class ExamResultResource extends ResourceCollection
{
    protected string $baseUrl;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->baseUrl = url('/'); // Initialize base URL
    }

    /**
     * Transform the resource collection into an array.
     */
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
        $userAns = $data['userAnswer'] ?? [];
        $startTime = $data['startDate'];
        $courseID = $data['courseID'];
        $questionTimeSetting = Setting::where('key', 'time_per_questions')->first();
        $examNumberSetting = Setting::where('key', 'question_numbers_perExam')->first();

        $questionTime = $questionTimeSetting ? $questionTimeSetting->value : 0;
        $examNumber = $examNumberSetting ? $examNumberSetting->value : 0;
        $examTime = $questionTime * $examNumber;

        $jalaliStartDate = $startTime ? convertDateTimeGregorianToJalaliDateTime($startTime) : null;

        if (isset($studentInfo['avatar']) && $studentInfo['avatar']) {
            // Decode the JSON string to get the avatar details
            $avatarData = json_decode($studentInfo['avatar'], true);

            // Check if the decoded data is valid and contains the 'slug' field
            if (is_array($avatarData) && isset($avatarData['slug'])) {
                // If the slug already contains the full URL, use it directly
                if (filter_var($avatarData['slug'], FILTER_VALIDATE_URL)) {
                    $studentInfo['avatar'] = $avatarData['slug'];
                } else {
                    // Otherwise, construct the URL using the baseUrl
                    $studentInfo['avatar'] = $this->baseUrl . '/' . ltrim($avatarData['slug'], '/');
                }
            } else {
                // If the avatar data is invalid, set it to null or a default value
                $studentInfo['avatar'] = null;
            }
        }

        $groupedAnswers = collect($data['answerSheet'])
            ->groupBy('questionID')
            ->map(function ($answers, $questionID) use ($userAns) {
                $correctAnswers = $answers->filter(function ($answer) {
                    return ($answer->isCorrect ?? false) == 1;
                })->pluck('optionID')->toArray();

                $userAnswer = collect($userAns['questions'])->firstWhere('question_id', $questionID);
                $userOptionID = $userAnswer['option_id'] ?? null;

                $hasIncorrect = false;

                $allAnswers = $answers->map(function ($answer) use ($correctAnswers, $userOptionID, &$hasIncorrect) {
                    $optionID = $answer->optionID ?? null;
                    $status = 'empty';

                    if ($optionID === $userOptionID && in_array($optionID, $correctAnswers)) {
                        $status = 'correct';
                    } elseif ($optionID === $userOptionID) {
                        $status = 'incorrect';
                        $hasIncorrect = true;
                    } elseif (in_array($optionID, $correctAnswers)) {
                        $status = 'missed';
                    }

                    return [
                        'option_id' => $optionID,
                        'option_title' => $answer->optionTitle ?? null,
                        'status' => $status,
                    ];
                })->values();

                if ($hasIncorrect) {
                    $allAnswers = $allAnswers->map(function ($answer) {
                        if ($answer['status'] === 'missed') {
                            $answer['status'] = 'correct';
                        }
                        return $answer;
                    });
                }

                return [
                    'question_id' => $questionID,
                    'question_title' => $answers->first()->questionTitle ?? null,
                    'all_answers' => $allAnswers,
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
            'courseID' => $courseID
        ];
    }
}
