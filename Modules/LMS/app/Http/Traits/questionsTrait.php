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


    public function questionList($id)
    {
        $query = Course::joinRelationship('chapters.lessons.questions.difficulty')
            ->joinRelationship('chapters.lessons.questions.options')
            ->joinRelationship('chapters.lessons.questions.repository')
            ->joinRelationship('chapters.lessons.questions.questionType')
            ->select([
                'questions.id as questionD',
                'questions.title as questionTitle',
                'question_types.name as questionTypeName',
                'difficulties.name as difficultyName',
                'repositories.name as repositoryName',
                'options.title as optionTitle',
                'chapters.title as chapterTitle',
                'lessons.title as lessonTitle',
                'courses.title as courseTitle',
                'options.is_correct as isCorrect'

            ])
            ->where('courses.id', $id)->get();
        $count = $this->count($id);

        return [
            'questionList' => $query,
            'count' => $count
        ];


    }

    public function count($id)
    {


//        return Course::withCount('chapters.lessons.questions')->where($id);


        $course = Course::joinRelationship('chapters.lessons.questions')->find($id);

        if (!$course) {
            return ['error' => 'Course not found'];
        }

        $chaptersCount = $course->chapters->count();
        $lessonsCount = $course->chapters->sum(fn($chapter) => $chapter->lessons->count());
        $questionsCount = $course->chapters->sum(fn($chapter) => $chapter->lessons->sum(fn($lesson) => $lesson->questions->count())
        );

        return [
            'chapters' => $chaptersCount,
            'lessons' => $lessonsCount,
            'questions' => $questionsCount,
        ];
    }


    public function updateQuestionWithOptions($questionID, $data, $options, $user)
    {
        $question = Question::findOrFail($questionID);

        $question->update([
            'title' => $data['title'],
            'question_type_id' => $data['questionTypeID'],
            'repository_id' => $data['repositoryID'],
            'lesson_id' => $data['lessonID'],
            'difficulty_id' => $data['difficultyID'],
            'update_date' => now(),
            'updater_id' => $user->id
        ]);

        $existingOptions = $question->options()->pluck('id')->toArray();
        $newOptionIDs = array_column($options, 'id');

        $optionsToDelete = array_diff($existingOptions, $newOptionIDs);
        Option::whereIn('id', $optionsToDelete)->delete();

        foreach ($options as $option) {
            if (isset($option['id'])) {
                Option::where('id', $option['id'])->update([
                    'title' => $option['title'],
                    'is_correct' => $option['is_correct']
                ]);
            } else {
                Option::create([
                    'title' => $option['title'],
                    'is_correct' => $option['is_correct'],
                    'question_id' => $question->id
                ]);
            }
        }

        return $question->load('options');
    }


}
