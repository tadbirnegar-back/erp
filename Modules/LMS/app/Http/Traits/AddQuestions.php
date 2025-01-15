<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\QuestionsEnum;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Question;

trait AddQuestions
{
    private static string $active = QuestionsEnum::ACTIVE->value;
    private static string $inactive = QuestionsEnum::EXPIRED->value;

    public function dropDowns()
    {
        $query = Course::query()->joinRelationship('chapters.lessons.questions.questionType')
            ->joinRelationship('chapters.lessons.questions.difficulty')
            ->joinRelationship('chapters.lessons.questions.repository');
        $query->select([
            'chapters.id as chapterID',
            'chapters.title as chapterTitle',
            'lessons.id as lessonID',
            'lessons.title as lessonTitle',
            'question_types.id as questionTypeID',
            'question_types.name as questionTypeName',
            'difficulties.id as difficultyID',
            'difficulties.name as difficultyName',
            'repositories.name as repositoryName',
            'repositories.id as repositoryID'
        ]);
        return $query->where('courses.id', 23)->get();
    }

    public function insertQuestion($data)
    {
        return Question::create([
            'title' => $data['title'],
            'question_type_id' => $data['questionTypeID'],
            'repository_id' => $data['repositoryID'],
            'lesson_id' => $data['lessonID'],
            'difficulty_id' => $data['difficultyID'],
            'create_date' => now(),
            'status_id' => [$this::$active,
                $this::$inactive
            ]

        ]);


    }


}
