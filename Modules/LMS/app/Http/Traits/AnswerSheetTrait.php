<?php

namespace Modules\LMS\app\Http\Traits;

use Carbon\Carbon;
use Modules\AAA\app\Models\User;
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


    public function storeAnswerSheet($examId, $student, $optionID, $data, $usedTime)
    {
        $score = $this->score($examId, $optionID);
        $status = $this->ScoreStatus($score);
        $finishDateTime = Carbon::parse(now());

        $answerSheet = AnswerSheet::create([
            'exam_id' => $examId,
            'finish_date_time' => $finishDateTime,
            'start_date_time' => now()->subSeconds($usedTime),
            'score' => $score,
            'student_id' => $student->student->id,
            'status_id' => $status->id,
        ]);

        $allQuestions = QuestionExam::where('exam_id', $examId)->pluck('question_id')->toArray();

        $sentQuestions = collect($data['questions'])->pluck('question_id')->toArray();

        $missingQuestions = array_diff($allQuestions, $sentQuestions);

        $optionIDs = [];
        foreach ($data['questions'] as $qA) {
            if (empty($qA['option_id']) || empty($qA['question_id'])) {
                $value = null;
            } else {
                $option = Option::where('id', $qA['option_id'])->first();
                $value = $option ? $option->title : null;
                $optionIDs[] = $option->id;
            }

            Answers::create([
                'answer_sheet_id' => $answerSheet->id,
                'question_id' => $qA['question_id'] ?? null,
                'value' => $value
            ]);
        }

        foreach ($missingQuestions as $missingQuestion) {
            Answers::create([
                'answer_sheet_id' => $answerSheet->id,
                'question_id' => $missingQuestion,
                'value' => null
            ]);
        }

        $final = $this->final($answerSheet);
        $calculate = $this->calculatingAnswers($optionID, $answerSheet->id, $usedTime, $examId);
        $studentInfo = $this->student($student);

        return [
            'finalAns' => $final,
            'calculated' => $calculate,
            'student' => $studentInfo,
        ];
    }


    public function correctAnswers($optionID)
    {
        return Option::whereIn('id', $optionID)
            ->where('is_correct', 1)
            ->count();
    }

    public function falseAnswers($optionID)
    {
        return Option::whereIn('id', $optionID)
            ->where('is_correct', 0)
            ->count();
    }

    public function nullAnswers($answerSheet)
    {
        return Answers::where('answer_sheet_id', $answerSheet)
            ->whereNull('value')
            ->count();
    }

    public function questionCount($examId)
    {
        return QuestionExam::where('exam_id', $examId)
            ->count('question_id');
    }


    public function score($examId, $optionID)
    {
        $totalQuestions = QuestionExam::where('exam_id', $examId)->count();
        $correctAnswers = $this->correctAnswers($optionID);

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


    public function calculatingAnswers($optionID, $answerSheet, $usedTime, $examId)
    {
        $correctAnswers = $this->correctAnswers($optionID);
        $falseAnswers = $this->falseAnswers($optionID);
        $nullAnswers = $this->nullAnswers($answerSheet);
        $questionCount = $this->questionCount($examId);
        $score = $this->score($examId, $optionID);

        return [
            'score' => $score,
            'correct' => $correctAnswers,
            'false' => $falseAnswers,
            'null' => $nullAnswers,
            'allQuestions' => $questionCount,
            'usedTime' => $usedTime
        ];
    }

    public function final($answerSheet)
    {

        $query = AnswerSheet::joinRelationship('answers.questions.options')
            ->joinRelationship('status');

        $query->select([
            'answer_sheets.score as answerSheetScore',
            'statuses.name as statusName',
            'answer_sheets.start_date_time as startDateTime',
            'answer_sheets.finish_date_time as finishDateTime',
            'options.id as optionID',
            'options.is_correct as isCorrect',
            'questions.id as questionID',
            'questions.title as questionTitle',
            'options.title as optionTitle',
        ])->where('answer_sheets.id', $answerSheet->id);
        return $query->get();
    }

    public function student($student)
    {

        $query = User::query()->joinRelationship('person.avatar')
            ->select([
                'persons.display_name as name',
                'files.slug as avatar'
            ])
            ->where('users.id', $student->id)
            ->first();
        return [$query, $student];
    }


}
