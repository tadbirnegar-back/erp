<?php

namespace Modules\LMS\app\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultResource extends JsonResource
{
    public function toArray($request)
    {
        $usedTimeInSeconds = $this['calculated']['usedTime'] ?? 0;
        $finishedTime = Carbon::parse($this['calculated']['finishedDateTime'] ?? now());
        $startedTime = $finishedTime->subSeconds($usedTimeInSeconds);
        $finishedTimeForShow = now();

        return [
            'finalAns' => collect($this['finalAns'])
                ->groupBy('questionID')->values()
                ->map(function ($group) {
                    $correctAnswers = $group->filter(function ($item) {
                        return $item['isCorrect'] == 1;
                    })->values();

                    $incorrectAnswers = $group->filter(function ($item) {
                        return $item['isCorrect'] == 0;
                    })->values();

                    return [
                        'correct' => $correctAnswers->map(function ($item) {
                            return [
                                'details' => [
                                    'optionID' => $item['optionID'] ?? null,
                                    'isCorrect' => $item['isCorrect'] ?? null,
                                    'questionTitle' => $item['questionTitle'] ?? null,
                                    'optionTitle' => $item['optionTitle'] ?? null,
                                ],
                            ];
                        }),

                        'incorrect' => $incorrectAnswers->map(function ($item) {
                            return [
                                'details' => [
                                    'optionID' => $item['optionID'] ?? null,
                                    'isCorrect' => $item['isCorrect'] ?? null,
                                    'questionTitle' => $item['questionTitle'] ?? null,
                                    'optionTitle' => $item['optionTitle'] ?? null,
                                ],
                            ];
                        }),
                    ];
                }),

            'calculated' => [
                'score' => $this['calculated']['score'] ?? null,
                'correct' => $this['calculated']['correct'] ?? null,
                'false' => $this['calculated']['false'] ?? null,
                'null' => $this['calculated']['null'] ?? null,
                'allQuestions' => $this['calculated']['allQuestions'] ?? null,
                'startedTime' => convertDateTimeGregorianToJalaliDateTime($startedTime->toDateTimeString()),
                'finishedTime' => convertDateTimeGregorianToJalaliDateTime($finishedTimeForShow->toDateTimeString()),
                'usedTime' => $this['calculated']['usedTime'] ?? 0,
            ],

            'student' => [
                'name' => $this['student'][0]['name'] ?? null,
                'avatar' => isset($this['student'][0]['avatar']) ? url($this['student'][0]['avatar']) : null,
            ],

        ];
    }
}
