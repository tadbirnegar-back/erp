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
        $uniqueCourseTitles = collect($this->resource['questionList'])->pluck('courseTitle')->first();

        return [
            'courseTitle' => $uniqueCourseTitles,

            'questionsList' => $groupedQuestions->map(function ($questions) {

                $firstQuestion = $questions->first();

                return [
                    'title' => $firstQuestion['questionTitle'],
                    'difficulty' => $firstQuestion['difficultyName'],
                    'repository' => $firstQuestion['repositoryName'],
                    'questionTypeName' => $firstQuestion['questionTypeName'],
                    'readOnly' => $firstQuestion['answerSheetID'] ? true : false,
                    'sources' => [
                        'chapterTitle' => $firstQuestion['chapterTitle'],
                        'lessonTitle' => $firstQuestion['lessonTitle'],
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
