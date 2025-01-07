<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\AnswerSheetStatusEnum;
use Modules\LMS\app\Models\Answers;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Student;
use function PHPUnit\Framework\isFalse;

trait AnswerSheetTrait
{
    public function answerSheetApprovedStatus()
    {
        return AnswerSheet::GetAllStatuses()->firstWhere('name', AnswerSheetStatusEnum::APPROVED->value);
    }

    public function answerSheetTakingExamStatus()
    {
        return AnswerSheet::GetAllStatuses()->firstWhere('name', AnswerSheetStatusEnum::TAKING_EXAM->value);
    }

    public function answerSheetDeclinedStatus()
    {
        return AnswerSheet::GetAllStatuses()->firstWhere('name', AnswerSheetStatusEnum::DECLINED->value);
    }

    public function StoringAnswerSheet($id, Student $student)
    {
        if ($this->correctAnswers() / )



        $store = AnswerSheet::create([
            'exam_id' => $id,
            'score'=>
        ]);


    }

    public function correctAnswers()
    {
        $query = AnswerSheet::joinRelationship('answers.questions.options');

        $correctAnswers = Answers::where('answer_sheet_id', $query->id)
            ->joinRelationship('options', function ($query) {
                $query->where('answers.value', '=', 'options.title')
                    ->where('options.is_correct', 1);
            })
            ->count();
        return $correctAnswers;

    }
}
