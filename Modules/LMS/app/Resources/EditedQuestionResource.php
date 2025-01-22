<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EditedQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $groupedQuestions = collect($this->resource['questionForEdit'])->groupBy('questionID');

        return [
            'questionsList' => $groupedQuestions->map(function ($questions) {
                $firstQuestion = $questions->first();

                return [
                    'title' => $firstQuestion['questionTitle'],
                    'questionID' => $firstQuestion['questionID'],
                    'difficulty' => $firstQuestion['difficultyName'],
                    'difficultyID' => $firstQuestion['difficultyID'],
                    'repository' => $firstQuestion['repositoryName'],
                    'repositoryID' => $firstQuestion['repositoryID'],
                    'questionType' => $firstQuestion['questionTypeName'],
                    'questionTypeID' => $firstQuestion['questionTypeID'],
                    'sources' => [
                        'chapterTitle' => $firstQuestion['chapterTitle'],
                        'chapterID' => $firstQuestion['chapterID'],
                        'lessonTitle' => $firstQuestion['lessonTitle'],
                        'lessonID' => $firstQuestion['lessonID']
                    ],

                    'options' => $questions->map(function ($question) {
                        return [
                            'titleOfOptions' => $question['optionTitle'],
                            'isCorrect' => $question['isCorrect'] == 1 ? 'correct' : 'incorrect',
                        ];
                    }),
                ];
            })->values(),

        ];
    }
}
