<?php

namespace training\Http\Traits;

use Modules\LMS\app\Enums\QuestionsEnum;
use Modules\LMS\app\Models\Question;

trait QuestionTrait
{
    public function storeQuestion($data)
    {

        $status = $this->questionActiveStatus();
        $question = new Question();
        $question->title = $data['title'] ?? null;
        $question->creator_id = $data['creatorId'] ?? null;
        $question->difficulty_id = $data['difficultyId'] ?? null;
        $question->lesson_id = $data['lessonId'] ?? null;
        $question->question_type_id = $data['questionTypeId'] ?? null;
        $question->repository_id = $data['repositoryId'] ?? null;
        $question->status_id = $status->id;
        $question->create_date = $data['createDate'] ?? null;
        $question->save();
        return $question->load('lesson', 'creator', 'difficulty', 'questionType', 'status', 'repository');

    }

    public function questionActiveStatus()
    {
        return Question::status()->firstWhere('name', '=', QuestionsEnum::ACTIVE->value);
    }

    public function questionInActiveStatus()
    {
        return Question::status()->firstWhere('name', QuestionsEnum::IN_ACTIVE->value);
    }
}
