<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\FormGMS\app\Models\Option;
use Modules\LMS\app\Http\Enums\AnswerSheetStatusEnum;
use Modules\LMS\app\Models\Answers;
use Modules\LMS\app\Models\AnswerSheet;
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


    public function storeAnswerSheet($examId, $student, $optionId, $data)
    {
        $finish = now();
        $start = now();
        $score = $this->score($examId, $optionId);
        $status = $this->ScoreStatus($score);
        $answerSheet = AnswerSheet::create([
            'exam_id' => $examId,
            'finish_date_time' => $finish,
            'start_date_time' => $start,
            'score' => $score,
            'student_id' => $student->student->id,
            'status_id' => $status->id
        ]);
        $questionIDs = isset($data['questionID']) && is_array($data['questionID'])
            ? $data['questionID']
            : (is_string($data['questionID']) ? explode(',', $data['questionID']) : []);
        foreach ($questionIDs as $questionID) {
            if (isset($data['options'][$questionID]) && !empty($data['options'][$questionID])) {
                $optionIds = array_filter(
                    is_string($data['options'][$questionID])
                        ? explode(',', $data['options'][$questionID])
                        : $data['options'][$questionID]
                );

                if (empty($optionIds)) {
                    Answers::create([
                        'answer_sheet_id' => $answerSheet->id,
                        'question_id' => $questionID,
                        'value' => null,
                    ]);
                    continue;
                }

                $options = Option::where('question_id', $questionID)
                    ->whereIn('id', $optionIds)
                    ->get();

                if (empty($options)) {
                    continue;
                }

                foreach ($options as $option) {
                    Answers::create([
                        'answer_sheet_id' => $answerSheet->id,
                        'question_id' => $questionID,
                        'value' => $option->title ?? null,
                    ]);
                }
            }

        }
        return $answerSheet;
    }

    public function correctAnswers($optionIds)
    {
        return Option::whereIn('id', (array)$optionIds)
            ->where('is_correct', 1)
            ->count();
    }


    public function score($examId, $optionId)
    {
        $totalQuestions = QuestionExam::where('exam_id', $examId)->count();
        $correctAnswers = $this->correctAnswers($optionId);

        if ($totalQuestions == 0) {
            return 0;
        }

        return ($correctAnswers / $totalQuestions) * 100;
    }

    public function ScoreStatus($score)
    {
        $approvedStatus = $this->answerSheetApprovedStatus();
        $declinedStatus = $this->answerSheetDeclinedStatus();

        if ($approvedStatus && $declinedStatus) {
            if ($score >= 50) {
                return $approvedStatus;
            } else {
                return $declinedStatus;
            }
        }

        return null;
    }
}
