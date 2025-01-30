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
        $allShow = collect($this->resource['allListToShow']);

        $groupedQuestions = collect($this->resource['questionForEdit'])->groupBy('questionID');

        return [
            'questionsList' => $groupedQuestions->map(function ($questions) {
                $firstQuestion = $questions->first();

                return [
                    'title' => $firstQuestion['questionTitle'],
                    'details' => [
                        'questionID' => $firstQuestion['questionID'],
                        'difficulty' => [
                            'difficulty' => $firstQuestion['difficultyName'],
                            'difficultyID' => $firstQuestion['difficultyID'],
                        ],
                        'repository' => [
                            'repository' => $firstQuestion['repositoryName'],
                            'repositoryID' => $firstQuestion['repositoryID'],
                        ],
                        'questionType' => [
                            'questionType' => $firstQuestion['questionTypeName'],
                            'questionTypeID' => $firstQuestion['questionTypeID'],
                        ],
                    ],
                    'sources' => [
                        'chapterTitle' => $firstQuestion['chapterTitle'],
                        'chapterID' => $firstQuestion['chapterID'],
                        'lessonTitle' => $firstQuestion['lessonTitle'],
                        'lessonID' => $firstQuestion['lessonID'],
                    ],
                    'options' => $questions->map(function ($question) {
                        return [
                            'optionID' => $question['optionID'],
                            'titleOfOptions' => $question['optionTitle'],
                            'isCorrect' => $question['isCorrect'] == 1 ? 'correct' : 'incorrect',
                        ];
                    }),
                ];
            })->first(),
            'allListToShow' => $this->makeCourseDatas($allShow),
        ];
    }

    /**
     * Generate course data structure.
     */
    private function makeCourseDatas($data)
    {
        return $data
            ->groupBy('chapterID')
            ->map(function ($lessons, $chapterID) {
                $chapter = $lessons->first();

                return [
                    'chapter_id' => $chapterID,
                    'chapter_title' => $chapter['chapterTitle'],
                    'lessons' => $lessons->map(function ($lesson) {
                        return [
                            'lesson_id' => $lesson['lessonID'],
                            'lesson_title' => $lesson['lessonTitle'],
                        ];
                    })->values()->toArray(),
                ];
            })->values()->toArray();
    }
}
