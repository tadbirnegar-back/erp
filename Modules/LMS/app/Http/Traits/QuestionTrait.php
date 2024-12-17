<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Enums\QuestionsEnum;
use Modules\LMS\app\Models\Question;
use Modules\StatusMS\app\Models\Status;
use Nwidart\Modules\Collection;

trait QuestionTrait
{

    public function storeQuestion($data)
    {
        $dataToInsert = $this->questionDataPreparation($data);

        $question = Question::create([$dataToInsert]);

        return $question->load('lesson', 'creator', 'difficulty', 'questionType', 'status', 'repository');
    }


    public function questionDataPreparation(array|Collection $question)
    {

        if (is_array($question)) {
            $question = collect($question);
        }

        $status = $this->questionActiveStatus();
        $question = $question->map(fn($data) => [
            'title' => $data['title'] ?? null,
            'creator_id' => $data['creatorId'] ?? null,
            'difficulty_id' => $data['difficultyId'] ?? null,
            'lesson_id' => $data['lessonId'] ?? null,
            'question_type_id' => $data['questionTypeId'] ?? null,
            'repository_id' => $data['repositoryId'] ?? null,
            'status_id' => $status->id,
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
