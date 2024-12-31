<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\QuestionsEnum;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Question;
use Nwidart\Modules\Collection;

trait QuestionsTrait
{
    private static string $activeQuestionStatus = QuestionsEnum::ACTIVE->value;
    private static string $expired = QuestionsEnum::EXPIRED->value;

    public function storeQuestion($data, $user, $courseID)
    {
        $course = Course::find($courseID);
        $dataToInsert = $this->questionDataPreparation($data, $user, $courseID);

        $question = Course::query()
            ->joinRelationship('chapters')
            ->joinRelationship('lessons.questions')
            ->joinRelationship('lessons.questions.creator',)
            ->joinRelationship('lessons.questions.difficulty')
            ->joinRelationship('lessons.questions.questionType')
            ->joinRelationship('lessons.questions.status', 'status_alias')
            ->joinRelationship('lessons.questions.repository', 'repository_alias');
        $question = Question::create($dataToInsert);

        $question->select([
            'questions.title as title',
            'lessons.id as lessonID',
            'chapters.id as chapterID',
            'difficulty.id as difficultyID',
            'question_type.id as questionTypeID',
            'repository.id as repositoryID',
            'status.name as statusName',
            'status.class_name as statusClassName',
            'question_type.name as questionTypeName',
            'difficulty.name as difficultyName',
            'creator_alias.id as creatorID',
            'creators.id as creatorID',


        ]);
        return $question;


    }


    public function questionDataPreparation(array|Collection $question, $creator, $course)
    {
        if (is_array($question)) {
            $question = collect($question);
        }

        $status = $this->activeQuestionStatus();
        $question = $question->map(fn($data) => [
            'title' => $data['title'] ?? null,
            'creator_id' => $creator->id ?? null,
            'difficulty_id' => $data['difficultyID'] ?? null,
            'lesson_id' => $data['lessonID'] ?? null,
            'question_type_id' => $data['questionTypeID'] ?? null,
            'repository_id' => $data['repositoryID'] ?? null,
            'status_id' => $status->id ?? null,
            'create_date' => $data['createDate'] ?? now(),
            'chapter_id' => $data['chapterID'] ?? null,
            'course_id' => $course->id ?? null,
        ])->first();

        return $question;
    }

    public function UpdateQuestion($data, Question $question)
    {
        $question->title = $data['title'];
        $question->creator_id = $data['creatorID'];
        $question->difficulty_id = $data['difficultyID'];
        $question->lesson_id = $data['lessonID'];
        $question->question_type_id = $data['questionTypeID'];
        $question->repository_id = $data['repositoryID'];
        $question->status_id = $data['statusID'];
        $question->create_date = $data['createDate'];
        $question->chapter_id = $data['chapterID'];
        $question->save();
        return $question;

    }

    public function deleteQuestionRecord(int $id): bool
    {
        $QuestionRecord = Question::find($id);
        if ($QuestionRecord) {
            $status = $this->InactiveQuestionStatus();
            $QuestionRecord->status_id = $status->id;
            $QuestionRecord->save();
            return true;
        }

        return false;
    }


    public function activeQuestionStatus()
    {
        return Question::GetAllStatuses()
            ->firstWhere('name', '=', self::$activeQuestionStatus);
    }


    public function InactiveQuestionStatus()
    {
        return Question::GetAllStatuses()
            ->firstWhere('name', '=', self::$expired);
    }


}

