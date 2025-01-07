<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\AnswerSheetStatusEnum;
use Modules\LMS\app\Models\Answers;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Student;
use Modules\SettingsMS\app\Models\Setting;
use Modules\StatusMS\app\Models\Status;

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

    public function StoringAnswerSheet($id, Student $student, $auth)
    {
        $examNumberSetting = Setting::where('key', 'question_numbers_perExam')->first();
        $examNumber = $examNumberSetting ? $examNumberSetting->value : 0;

        $score = ($this->correctAnswers() / $examNumber) * 100;
        $status = Status::where('name', $score >= 50 ? 'قبول شده' : 'رد شده')->first();


        $answerSheet = AnswerSheet::create([
            'exam_id' => $id,
            'score' => $score,
            'status_id' => $status?->id,
            'student_id' => $student->id,
        ])->where('student_id', $auth->student->id);

        $questionsWithAnswers = $this->value();

        foreach ($questionsWithAnswers as $question) {
            Answers::create([
                'answer_sheet_id' => $answerSheet->id,
                'question_id' => $question->questionID,
                'value' => $question->optionID,
            ]);
        }
        return $answerSheet;

    }

    public function getValue()
    {
        $query = AnswerSheet::joinRelationship('answers.questions.options');
        $query->select([
            'question_id as questionID',
            'option_id ad optionID',
        ])->get();
        return $query;

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
