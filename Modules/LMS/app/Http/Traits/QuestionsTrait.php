<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\Question;
use Nwidart\Modules\Collection;

trait QuestionsTrait
{
    private static string $activeQuestionStatus = 'فعال';
    private static string $inActiveQuestionStatus = 'غیرفعال';

    public function storeQuestion($data, $user)
    {
        $dataToInsert = $this->questionDataPreparation($data, $user);

        /**
         * @var Question $question
         */
        $question = Question::create($dataToInsert->first());

        $a = Question::joinRelationshipUsingAlias('lesson', 'lesson_alias')
            ->joinRelationshipUsingAlias('creator', 'creator_alias')
            ->joinRelationshipUsingAlias('difficulty', 'difficulty_alias')
            ->joinRelationshipUsingAlias('questionType', 'question_type_alias')
            ->joinRelationshipUsingAlias('status', 'status_alias')
//            ->joinRelationshipUsingAlias('chapter', 'chapter_alias')
            ->joinRelationshipUsingAlias('repository', 'repository_alias')
            ->addSelect([
                'lesson_alias.title as lesson_title',
                'creator_alias.id as creator_id',
                'difficulty_alias.name as difficulty_name',
                'difficulty_alias.id as difficulty_id',
                'question_type_alias.name as question_type_name',
                'question_type_alias.id as question_type_id',
                'repository_alias.name as repository_name',
                'repository_alias.id as repository_id',
                'status_alias.name as status_name',
                'status_alias.class_name as status_class_name',
//                'chapter_alias.title as chapter_title',
            ])
            ->find($question->id);

//        dd($a);
        return $a;
    }

//        return $question->load('lesson', 'creator', 'difficulty', 'questionType', 'status', 'repository');


    public function questionDataPreparation(array|Collection $question, $creator)
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

        ]);

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
            ->firstWhere('name', '=', self::$inActiveQuestionStatus);
    }


}

