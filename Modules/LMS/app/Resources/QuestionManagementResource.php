<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionManagementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        $groupedQuestions = collect($this->resource['questionList'])->groupBy('questionID');
        return [
            'questionsList' => $groupedQuestions->map(function ($questions) {

                $firstQuestion = $questions->first();

                return [
                    'title' => $firstQuestion['questionTitle'],
                    'difficulty' => $firstQuestion['difficultyName'],
                    'repository' => $firstQuestion['repositoryName'],
                    'sources' => [
                        'chapterTitle' => $firstQuestion['chapterTitle'],
                        'lessonTitle' => $firstQuestion['lessonTitle'],
                    ],
                    'course' => [
                        'title' => 'دوره آموزش' . ' ' . $firstQuestion['courseTitle']
                    ],
                    'options' => $questions->map(function ($question) {
                        return [
                            'titleOfOptions' => $question['optionTitle'],
                            'isCorrect' => $question['isCorrect'] == 1 ? 'correct' : 'incorrect',
                        ];
                    }),
                ];
            })->values(),
            'count' => $this->resource['count'],
        ];
    }
}
