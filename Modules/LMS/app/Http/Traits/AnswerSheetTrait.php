<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\AnswerSheetStatusEnum;
use Modules\LMS\app\Models\Answers;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Option;
use Modules\LMS\app\Models\QuestionExam;
use Modules\LMS\app\Models\Student;
use Modules\SettingsMS\app\Models\Setting;

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


    public function StoringAnswerSheet($examId, Student $student, $auth, $data)
    {


    }


    public function getValue($id, $data)
    {
        $option = Option::findOrFail($data['optionID']);

        Answers::create([
            'answer_sheet_id' => $id,
            'question_id' => $data['questionID'],
            'value' => $option->title,
        ]);
    }


    public function correctAnswers($optionID)
    {
        $correct = Option::where('id', $optionID)
            ->where('is_correct', 1)
            ->count();

        return $correct;
    }


    public function score($examId, array $optionIDs)
    {
        $totalQuestions = QuestionExam::where('exam_id', $examId)->count();
        $correctAnswers = $this->correctAnswers($optionIDs);

        if ($totalQuestions == 0) {
            return 0;
        }

        return ($correctAnswers / $totalQuestions) * 100;
    }


    public function ScoreStatus($score)
    {
        if ($score >= 50) {
            return $this->answerSheetApprovedStatus();
        } else {
            return $this->answerSheetDeclinedStatus();
        }
    }


    public function examNumbers()
    {
        $examNumberSetting = Setting::where('key', 'question_numbers_perExam')->first();
        $examNumber = $examNumberSetting ? $examNumberSetting->value : 0;
        return $examNumber;

    }
}
