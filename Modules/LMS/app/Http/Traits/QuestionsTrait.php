<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\QuestionsEnum;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Option;
use Modules\LMS\app\Models\Question;

trait QuestionsTrait
{
    use LessonTrait;

    private static string $active = QuestionsEnum::ACTIVE->value;
    private static string $inactive = QuestionsEnum::EXPIRED->value;

    public function dropDowns($quesionId)
    {
        $question = Question::find($quesionId);
        $question->load('course');
        $courseID = $question->course->id;
        return Course::with('chapters.allActiveLessons')->find($courseID);

    }


    public function dropDownsAddQuestion($courseId)
    {
        return Course::with('chapters.allActiveLessons')->find($courseId);
    }

    public function insertQuestionWithOptions($data, $options, $courseID, $user, $repositoryIDs)
    {

        $course = Course::find($courseID);
        $courseID = $course->id;
        $status = $this->questionActiveStatus();
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
        $status = $this->questionActiveStatus();

        $course = Course::find($id);

        $query = Course::leftJoinRelationship('chapters.allActiveLessons.questions', function ($join) use ($status) {
            $join->where('questions.status_id', $status->id);
        })
            ->leftJoinRelationship('chapters.allActiveLessons.questions.difficulty')
            ->leftJoinRelationship('chapters.allActiveLessons.questions.options')
            ->leftJoinRelationship('chapters.allActiveLessons.questions.repository')
            ->leftJoinRelationship('chapters.allActiveLessons.questions.questionType')
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
                'lessons.title as lessonTitle',
                'options.is_correct as isCorrect',
                'answers_alias.question_id as answerQuestionID',
            ])
            ->distinct('questions.id')
            ->whereNotNull('questions.id')
            ->where('courses.id', $id)
            ->get();

        $count = $this->count($id);

        return [
            'questionList' => $query,
            'count' => $count,
            'course' => $course
        ];

    }

    public function count($id)
    {
        $course = Course::query()
            ->withCount('chapters')
            ->withCount('allActiveLessons')
            ->withCount('questions')
            ->find($id);

        return [
            'chapters' => $course->chapters_count,
            'lessons' => $course->all_active_lessons_count,
            'questions' => $course->questions_count
        ];
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

            if (!empty($delete)) {
                $this->deleteOptions($delete);
            }


            if (!empty($options)) {
                Option::where('question_id', $questionID)->update(['is_correct' => 0]);

                foreach ($options as $option) {
                    if (isset($option['option_id'])) {
                        Option::where('id', $option['option_id'])
                            ->update([
                                'is_correct' => $option['is_correct'],
                                'title' => $option['title'],
                            ]);

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
        return Option::whereIn('id', $option_ids)->delete();


    }


    public function showEditedQuestion($questionID)
    {
        $question = Question::find($questionID);
        $courseData = $question->load('course');
        $courseID = $courseData->course->id;
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
            ->distinct('chapters.id')
            ->where('questions.id', $questionID)->get();

        $questions = Course::joinRelationship('chapters.lessons')
            ->select([

                'chapters.title as chapterTitle',
                'chapters.id as chapterID',
                'lessons.title as lessonTitle',
                'lessons.id as lessonID',
                'courses.id as courseID',


            ])->where('courses.id', $courseID)->get();


        return [
            'questionForEdit' => $question,
            'allListToShow' => $questions,
        ];
    }

    /**
     * @return string
     */
    public function questionDelete($questionID): string
    {
        $status = $this->questionInActiveStatus();

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


    public function questionActiveStatus()
    {
        return Question::GetAllStatuses()->firstWhere('name', QuestionsEnum::ACTIVE->value);
    }

    public function questionInActiveStatus()
    {
        return Question::GetAllStatuses()->firstWhere('name', QuestionsEnum::EXPIRED->value);
    }


}
