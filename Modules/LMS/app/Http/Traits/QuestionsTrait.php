<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Enums\QuestionsEnum;
use Modules\LMS\app\Models\Question;
use Modules\StatusMS\app\Models\Status;
use Nwidart\Modules\Collection;

trait QuestionsTrait
{

    public function storeQuestion($data)
    {
        $dataToInsert = $this->questionDataPreparation($data);

        $question = Question::create($dataToInsert);

        return $question->load('lesson', 'creator', 'difficulty', 'questionType', 'status', 'repository');
    }


    public function questionDataPreparation(array|Collection $question)
    {

        if (is_array($question)) {
            $question = collect($question);
        }

        $status = ([$this->questionActiveStatus(), $this->questionInActiveStatus()]);
        $question = $question->map(fn($data) => [
            'title' => $data['title'] ?? null,
            'creator_id' => $data['creatorID'] ?? null,
            'difficulty_id' => $data['difficultyID'] ?? null,
            'lesson_id' => $data['lessonID'] ?? null,
            'question_type_id' => $data['questionTypeID'] ?? null,
            'repository_id' => $data['repositoryID'] ?? null,
            'status_id' => $status->firstWhere('name', QuestionsEnum::ACTIVE->value)->id,
            'create_date' => $data['createDate'] ?? now(),
        ]);
        return $question;

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

