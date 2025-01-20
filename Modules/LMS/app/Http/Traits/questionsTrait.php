<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\QuestionsEnum;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Option;
use Modules\LMS\app\Models\Question;
use Modules\StatusMS\app\Models\Status;

trait questionsTrait
{
    private static string $active = QuestionsEnum::ACTIVE->value;
    private static string $inactive = QuestionsEnum::EXPIRED->value;

    public function dropDowns($courseID)
    {
        $query = Course::query()->joinRelationship('chapters.lessons.questions.difficulty')
            ->joinRelationship('chapters.lessons.questions.questionType')
            ->joinRelationship('chapters.lessons.questions.repository');
        $query->select([
            'chapters.id as chapterID',
            'chapters.title as chapterTitle',
            'lessons.id as lessonID',
            'lessons.title as lessonTitle',
            'difficulties.name as difficultyName',
            'difficulties.id as difficultyID',
            'question_types.id as questionTypeID',
            'question_types.name as questionTypeName',
            'repositories.id as repoID',
            'repositories.name as repoName'
        ]);
        return $query->where('courses.id', $courseID)->get();
    }

    public function insertQuestionWithOptions($data, $options, $courseID, $user)
    {
        $status = Status::whereIn('name', [$this::$active, $this::$inactive])->first();


        $question = Question::create([
            'title' => $data['title'],
            'question_type_id' => $data['questionTypeID'],
            'repository_id' => $data['repositoryID'],
            'lesson_id' => $data['lessonID'],
            'difficulty_id' => $data['difficultyID'],
            'create_date' => now(),
            'status_id' => $status->id,
//            'creator_id' => $user->user->id
        ]);

        if ($question) {
            $optionsToInsert = [];
            foreach ($options as $option) {
                $optionsToInsert[] = [
                    'title' => $option['title'],
                    'is_correct' => $option['is_correct'],
                    'question_id' => $question->id,
                ];
            }

            Option::insert($optionsToInsert);
        }

        return $question->where('courses.id', $courseID)->get();
    }


}
