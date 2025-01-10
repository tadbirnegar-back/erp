<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\AnswerSheetStatusEnum;
use Modules\LMS\app\Models\Answers;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Option;
use Modules\LMS\app\Models\QuestionExam;

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


    public function StoringAnswerSheet($examId, $student, $optionIDs,)
    {
        $score = $this->score($examId, $optionIDs);
        $status = $this->ScoreStatus($score);

        $finish = now();
        $start = now();
        $answerSheet = AnswerSheet::create([
            'score' => $score,
            'student_id' => $student->id,
            'exam_id' => $examId,
            'finish_date_time' => $finish,
            'start_date_time' => $start,
            'status_id' => $status->id,
        ]);

        foreach ($optionIDs as $questionID => $optionID) {
            $option = Option::findOrFail($optionID);

            Answers::create([
                'answer_sheet_id' => $answerSheet->id,
                'question_id' => $questionID,
                'value' => $option->title,
            ]);
        }

        return $answerSheet;
    }

    public function correctAnswers($optionIDs)
    {
        $correct = Option::whereIn('id', $optionIDs)
            ->where('is_correct', 1)
            ->count();

        return $correct;
    }

    public function score($examId, $optionIDs)
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

}
