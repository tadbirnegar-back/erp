<?php

namespace training\Http\Traits;

use Illuminate\Support\Collection;
use Modules\LMS\app\Enums\QuestionsEnum;
use Modules\LMS\app\Models\Question;

trait QuestionTrait
{
    public function storeQuestion($data)
    {
        $dataToInsert = $this->questionDataPreparation($data);

        $question = Question::insert($dataToInsert->toArray());

        return $question;
    }


//    public function storeQuestion($data)
//    {

//        $status = $this->questionActiveStatus();
//        $question = new Question();
//        $question->title = $data['title'] ?? null;
//        $question->creator_id = $data['creatorId'] ?? null;
//        $question->difficulty_id = $data['difficultyId'] ?? null;
//        $question->lesson_id = $data['lessonId'] ?? null;
//        $question->question_type_id = $data['questionTypeId'] ?? null;
//        $question->repository_id = $data['repositoryId'] ?? null;
//        $question->status_id = $status->id;
//        $question->create_date = $data['createDate'] ?? null;
//        $question->save();
//        return $question->load('lesson', 'creator', 'difficulty', 'questionType', 'status', 'repository');
    public function questionDataPreparation(array|Collection $question)
    {
        if (is_array($question)) {
            $question = collect($question);
        }
        $status = $this->questionActiveStatus();

        $question = $question->map(fn($data) => [
            'id' => $data['id'] ?? null,
            'title' => $data['title'],
            'creator_id' => $data['creatorID'],
            'difficulty_id' => $data['difficultyID'],
            'lesson_id' => $data['lessonID'] ?? null,
            'question_type_id' => $data['questionTypeID'],
            'repository_id' => $data['repoID'] ?? null,
            'status_id' => $status->id,
            'create_date' => $data['salary'] ?? null,

        ],
        );
        return $question;
    }

    public function editQuestion($data)
    {

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
