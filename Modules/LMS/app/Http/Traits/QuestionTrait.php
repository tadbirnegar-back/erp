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
        $question->title = $data['title'];
        $question->creator_id = $data['creator_id'];
        $question->difficulty_id = $data['difficulty_id'] ?? null;
        $question->lesson_id = $data['lesson_id'] ?? null;
        $question->question_type_id = $data['question_type_id'] ?? null;
        $question->repository_id = $data['repository_id'] ?? null;
        $question->status_id = $status->id;
        $question->create_date = $data['create_date'] ?? null;
        $question->save();
        return $question->load('lesson', 'creator', 'difficulty', 'questionType', 'status', 'repository');

    }

    public function questionActiveStatus()
    {
        return Question::status()->firstWhere('name', QuestionsEnum::ACTIVE->value);
    }

    public function questionInActiveStatus()
    {
        return Question::status()->firstWhere('name', QuestionsEnum::IN_ACTIVE->value);
    }
}
