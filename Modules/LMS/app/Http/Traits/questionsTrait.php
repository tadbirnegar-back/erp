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
        $status = Status::where('name', $this::$active, Question::class)->first();

        $query = Course::leftJoinRelationship('chapters.lessons.lessonStatus');
        $query->select([
            'chapters.id as chapterID',
            'chapters.title as chapterTitle',
            'lessons.id as lessonID',
            'lessons.title as lessonTitle',
            'status_lesson.status_id as stu_id'
        ]);
        return $query->where('courses.id', $courseID)
            ->where('status_lesson.status_id', $status->id)
            ->get();
    }

    public function insertQuestionWithOptions($data, $options, $courseID, $user, $repositoryIDs)
    {
        $status = Status::whereIn('name', [$this::$active, $this::$inactive])->first();
        $questions = [];
        foreach ($repositoryIDs as $repositoryID) {
            $question = Question::create([
                'title' => $data['title'],
                'question_type_id' => $data['questionTypeID'],
                'repository_id' => $repositoryID,
                'lesson_id' => $data['lessonID'],
                'difficulty_id' => $data['difficultyID'],
                'create_date' => now(),
                'status_id' => $status->id,
                'creator_id' => $user->id,
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
                $questions[] = $question;
            }
        }
        $question->joinRelationship('lesson.chapter.course')
            ->where('courses.id', $courseID)
            ->get();

        return $question;
    }


    public function questionList($id)
    {
        $status = Status::where('name', self::$active)->firstOrFail();

        $query = Course::joinRelationship('chapters.lessons.questions.difficulty')
            ->joinRelationship('chapters.lessons.questions.options')
            ->joinRelationship('chapters.lessons.questions.repository')
            ->joinRelationship('chapters.lessons.questions.questionType')
            ->leftJoinRelationship('chapters.lessons.questions.answers.answerSheet', [
                'chapters' => fn($join) => $join->as('chapters_alias'),
                'lessons' => fn($join) => $join->as('lessons_alias'),
                'answers' => fn($join) => $join->as('answers_alias'),
                'answerSheet' => fn($join) => $join->as('answer_sheet_alias')

            ])
            ->select([
                'questions.id as questionID',
                'questions.title as questionTitle',
                'question_types.name as questionTypeName',
                'difficulties.name as difficultyName',
                'repositories.name as repositoryName',
                'options.title as optionTitle',
                'chapters.title as chapterTitle',
                'lessons_alias.title as lessonTitle',
                'courses.title as courseTitle',
                'options.is_correct as isCorrect',
                'answers_alias.question_id as answerQuestionID',
            ])
            ->where('courses.id', $id)
            ->where('questions.status_id', $status->id)
            ->get();
        $count = $this->count($id);
        return [
            'questionList' => $query,
            'count' => $count
        ];


    }

    public function count($id)
    {
        $status = Status::where('name', $this::$active)->firstOrFail();

        $course = Course::with(['chapters.lessons.questions' => function ($query) use ($status) {
            $query->where('status_id', $status->id);
        }])->find($id);

        $activeLessons = \DB::table('lessons')
            ->join('status_lesson', 'lessons.id', '=', 'status_lesson.lesson_id')
            ->where('status_lesson.status_id', $status->id)
            ->pluck('lessons.id');

        $chaptersCount = $course->chapters->count();

        $lessonsCount = $course->chapters->sum(function ($chapter) use ($activeLessons) {
            return $chapter->lessons->whereIn('id', $activeLessons)->count();
        });

        $questionsCount = $course->chapters->sum(fn($chapter) => $chapter->lessons->sum(fn($lesson) => $lesson->questions->where('status_id', $status->id)->count()));


        return [
            'chapters' => $chaptersCount,
            'lessons' => $lessonsCount,
            'questions' => $questionsCount];
    }

    public function updateQuestionWithOptions($questionID, $data, $options, $user, $delete, $repositoryIDs)
    {
        $question = Question::find($questionID);

        foreach ($repositoryIDs as $repositoryID) {

            $question->update([
                'title' => $data['title'],
                'question_type_id' => $data['questionTypeID'] ?? $question->question_type_id,
                'repository_id' => $repositoryID,
                'lesson_id' => $data['lessonID'] ?? $question->lesson_id,
                'difficulty_id' => $data['difficultyID'] ?? $question->difficulty_id,
                'create_date' => now(),
                'creator_id' => $user->id
            ]);

            foreach ($delete as $optionToDelete) {
                $this->deleteOptions($optionToDelete);
            }

            if (!empty($options)) {
                Option::where('question_id', $questionID)->update(['is_correct' => 0]);

                foreach ($options as $option) {
                    if (isset($option['option_id'])) {
                        Option::where('id', $option['option_id'])
                            ->update(['is_correct' => $option['is_correct']]);
                    } else {
                        Option::create([
                            'title' => $option['title'] ?? 'Default Title',
                            'is_correct' => $option['is_correct'],
                            'question_id' => $questionID,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        return $question;
    }


    public function deleteOptions(array $option_ids)
    {
        Option::whereIn('id', $option_ids)->delete();
        return response()->json(['message' => 'Options deleted successfully.'], 200);

    }


    public function showEditedQuestion($questionID)
    {
        $question = Course::joinRelationship('chapters.lessons.questions.difficulty')
            ->joinRelationship('chapters.lessons.questions.options')
            ->joinRelationship('chapters.lessons.questions.repository')
            ->joinRelationship('chapters.lessons.questions.questionType')
            ->select([
                'questions.id as questionID',
                'questions.title as questionTitle',
                'question_types.name as questionTypeName',
                'question_types.id as questionTypeID',
                'difficulties.name as difficultyName',
                'difficulties.id as difficultyID',
                'repositories.name as repositoryName',
                'repositories.id as repositoryID',
                'options.title as optionTitle',
                'options.id as optionID',
                'chapters.title as chapterTitle',
                'chapters.id as chapterID',
                'lessons.title as lessonTitle',
                'lessons.id as lessonID',
                'courses.title as courseTitle',
                'courses.id as courseID',
                'options.is_correct as isCorrect'

            ])
            ->where('questions.id', $questionID)->get();

        $questions = Course::joinRelationship('chapters.lessons')
            ->select([

                'chapters.title as chapterTitle',
                'chapters.id as chapterID',
                'lessons.title as lessonTitle',
                'lessons.id as lessonID',
                'courses.id as courseID',


            ])->first();

        $id = $questions->courseID;
        $all = $this->showAll($id);
        return ['questionForEdit' => $question,
            'allListToShow' => $all,
        ];
    }

    /**
     * @return string
     */
    public static function questionDelete($questionID): string
    {
        $status = Status::where('name', self::$inactive)->firstOrFail();

        $question = Question::findOrFail($questionID);
        $question->status_id = $status->id;

        $question->save();
        return $question;
    }

    public function showAll($id)
    {
        $questions = Course::joinRelationship('chapters.lessons')
            ->select([

                'chapters.title as chapterTitle',
                'chapters.id as chapterID',
                'lessons.title as lessonTitle',
                'lessons.id as lessonID',
                'courses.id as courseID',


            ])->where('courses.id', $id)->get();

        return $questions;
    }

}
