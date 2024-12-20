<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\Option;

trait OptionTrait
{
    private static string $activeQuestionStatus = 'فعال';
    private static string $expired = 'منسوخ شده';

    public function insertOptions($data, $questionID)
    {

        $option = Option::create([
            'question_id' => $questionID,
            'title' => $data['title'],
            'is_correct' => $data['isCorrect'],

        ]);
        return $option;
    }


    public function editOption(Option $option, $data)
    {
        $option->question_id = $data['questionID'];
        $option->title = $data['title'];
        $option->is_correct = $data['isCorrect'];
//        $option->create_date = $data['createDate'];
        $option->save();
        return $option;

    }

    public function deleteOption(int $id): bool
    {
        $OptionRecord = Option::find($id);
        if ($OptionRecord) {
            $OptionRecord->delete();
            return true;
        }

        return false;
    }

//        if ($OptionRecord) {
//            $status = $this->optionInActiveStatus();
//            $OptionRecord->status_id = $status->id;
//            $OptionRecord->save();
//
//            return true;
//        }
//
//        return false;


//    public function optionActiveStatus()
//    {
//        return Status::firstWhere('name', '=', self::$activeQuestionStatus);
//    }
//
//
//    public function optionInActiveStatus()
//    {
//        return Status::firstWhere('name', '=', self::$expired);
//    }


}

