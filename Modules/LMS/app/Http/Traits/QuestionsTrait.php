<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Enums\QuestionsEnum;
use Modules\LMS\app\Models\Question;
use Modules\StatusMS\app\Models\Status;
use Nwidart\Modules\Collection;

trait QuestionsTrait
{

    public function storeQuestion($data, $user)
    {
        $dataToInsert = $this->questionDataPreparation($data, $user);

        $question = Question::create($dataToInsert->first());

        return $question->load('lesson', 'creator', 'difficulty', 'questionType', 'status', 'repository',);
    }


    public function questionDataPreparation(array|Collection $question, $creator)
    {
        if (is_array($question)) {
            $question = collect($question);
        }

        $status = $this->questionActiveStatus();
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
            $status = $this->questionInActiveStatus();
            $QuestionRecord->status_id = $status->id;
            $QuestionRecord->save();
            return true;
        }

        return false;
    }

    public function questionActiveStatus()
    {
        return Status::firstWhere('name', QuestionsEnum::ACTIVE->value);
    }


    public function questionInActiveStatus()
    {
        return Status::firstWhere('name', QuestionsEnum::IN_ACTIVE->value);
    }


}

