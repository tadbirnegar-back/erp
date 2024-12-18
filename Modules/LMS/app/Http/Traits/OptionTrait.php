<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Enums\QuestionsEnum;
use Modules\LMS\app\Models\Option;
use Modules\StatusMS\app\Models\Status;

trait OptionTrait
{
    public function insertOptions($data)
    {
        $status = $this->questionActiveStatus();

        $option = Option::insert([
            'question_id' => $data['questionID'],
            'option' => $data['option'],
            'is_correct' => $data['isCorrect'],
            'create_date' => $data['createDate'],
            'status_id' => $status->id ?? null,

        ]);
        return $option;
    }

    public function editOption(Option $option, $data)
    {
        $option->question_id = $data['questionID'];
        $option->option = $data['option'];
        $option->is_correct = $data['isCorrect'];
        $option->create_date = $data['createDate'];
        $option->save();
        return $option;

    }

    public function deleteOption(int $id): bool
    {
        $OptionRecord = Option::find($id);

        if ($OptionRecord) {
            $status = $this->optionInActiveStatus();
            $OptionRecord->status_id = $status->id;
            $OptionRecord->save();

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

    public function optionActiveStatus()
    {
        return Status::firstWhere('name', QuestionsEnum::ACTIVE->value);
    }


    public function optionInActiveStatus()
    {
        return Status::firstWhere('name', QuestionsEnum::IN_ACTIVE->value);
    }


}

