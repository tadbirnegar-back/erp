<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultResource extends JsonResource
{
    public function toArray($request)
    {
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
                'correct' => $this['calculated']['correct'] ?? null,
                'false' => $this['calculated']['false'] ?? null,
                'null' => $this['calculated']['null'] ?? null,
                'allQuestions' => $this['calculated']['allQuestions'] ?? null,
                'startedTime' => $this['calculated']['startedDateTime'] ?? null,
                'finishedTime' => $this['calculated']['finishedDateTime'] ?? null,
                'usedTime' => $this['calculated']['usedTime'] ?? null,

            ],

            'student' => [
                'name' => $this['student'][0]['name'] ?? null,
                'avatar' => isset($this['student'][0]['avatar']) ? url($this['student'][0]['avatar']) : null,
            ],

        ];
    }
}
