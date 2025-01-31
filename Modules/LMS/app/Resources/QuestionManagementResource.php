<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class QuestionManagementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $questionIDs = collect($this->resource['questionList'])->pluck('questionID')->unique();

        $existingQuestionIDs = DB::table('question_exam')
            ->whereIn('question_id', $questionIDs)
            ->pluck('question_id')
            ->toArray();

        $groupedQuestions = collect($this->resource['questionList'])->groupBy('questionID');
        $uniqueCourseTitles = collect($this->resource['questionList'])->pluck('courseTitle')->first();

        return [
            'courseTitle' => $uniqueCourseTitles,

            'questionsList' => $groupedQuestions->map(function ($questions) use ($existingQuestionIDs) {
                $firstQuestion = $questions->first();
                $existsInExamQuestion = in_array($firstQuestion['questionID'], $existingQuestionIDs);

                return [
                    'title' => $firstQuestion['questionTitle'],
                    'questionID' => $firstQuestion['questionID'],
                    'difficulty' => $firstQuestion['difficultyName'],
                    'repository' => $firstQuestion['repositoryName'],
                    'questionTypeName' => $firstQuestion['questionTypeName'],
                    'deleteAble' => $existsInExamQuestion,
                    'editable' => $existsInExamQuestion,
//                    'existsInExamQuestion' => $existsInExamQuestion,

                    'sources' => [
                        'chapterTitle' => $firstQuestion['chapterTitle'],
                        'lessonTitle' => $firstQuestion['lessonTitle'],
                    ],

                    'options' => $questions->map(function ($question) {
                        return [
                            'titleOfOptions' => $question['optionTitle'],
                            'isCorrect' => $question['isCorrect'] == 1 ? 'correct' : 'incorrect',
                        ];
                    })->unique('titleOfOptions')->values(),
                ];
            })->values(),
            'count' => $this->resource['count'],
        ];
    }
}
