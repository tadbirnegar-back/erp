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
        $query = Course::query()->joinRelationship('chapters.lessons.questions');
        $query->select([
            'chapters.id as chapterID',
            'chapters.title as chapterTitle',
            'lessons.id as lessonID',
            'lessons.title as lessonTitle',
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
            'creator_id' => $user->id
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
        $question->joinRelationship('lesson.chapter.course')
            ->where('courses.id', $courseID)
            ->get();

        return $question;
    }

}
