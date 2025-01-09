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

        $statusName = $score >= 50 ? 'قبول شده' : 'رد شده';
        $status = Status::where('name', $statusName)->first();

        $answerSheet = AnswerSheet::create([
            'exam_id' => $id,
            'score' => $score,
            'status_id' => $status->id,
            'student_id' => $student->id,
        ]);

        if ($answerSheet->student_id !== $auth->student->id) {
            return response()->json(['error' => 'Unauthorized action'], 403);
        }

        $questionsWithAnswers = $this->getValue();

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
            'option_id as optionID'
        ])->get();
        return $query;

    }


    public function correctAnswers($id, Student $student)
    {
        $correctAnswers = Answers::where('student_id', $student->id)
            ->joinRelationship('options', function ($query) {
                $query->whereColumn('answers.value', 'options.title')
                    ->where('options.is_correct', 1);
            })
            ->where('answers.exam_id', $id)
            ->count();

        return $correctAnswers;
    }
}
