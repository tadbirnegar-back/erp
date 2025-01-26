<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\AAA\app\Models\User;
use Modules\FormGMS\app\Models\Option;
use Modules\LMS\app\Http\Enums\AnswerSheetStatusEnum;
use Modules\LMS\app\Models\Answers;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\QuestionExam;
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


    public function storeAnswerSheet($examId, $student, $optionID, $data)
    {

        $score = $this->score($examId, $optionID);
        $status = $this->ScoreStatus($score);

        $answerSheet = AnswerSheet::create([
            'exam_id' => $examId,
            'finish_date_time',
            'start_date_time',
            'score' => $score,
            'student_id' => $student->student->id,
            'status_id' => $status->id,
        ]);

        $qAs = $data['questions'];
        foreach ($qAs as $qA) {

            if (empty($qA['option_id'])) {
                $value = null;
            } else {

                $option = Option::where('id', $qA['option_id'])->first();

                $value = $option->title;
                $optionIDs[] = $option->id;
            }

            Answers::create([
                'answer_sheet_id' => $answerSheet->id,
                'question_id' => $qA['question_id'],
                'value' => $value
            ]);
        }
        $final = $this->final($answerSheet);
        $calculate = $this->calculatingAnswers($optionIDs, $answerSheet->id);
        $studentInfo = $this->student($student);

        return [
            'finalAns' => $final,
            'calculated' => $calculate,
            'student' => $studentInfo,
        ];

    }


    public function correctAnswers($optionIDs)
    {
        return Option::whereIn('id', $optionIDs)
            ->where('is_correct', 1)
            ->count();
    }

    public function falseAnswers($optionIDs)
    {
        return Option::whereIn('id', $optionIDs)
            ->where('is_correct', 0)
            ->count();
    }

    public function nullAnswers($answerSheet)
    {
        return Answers::where('answer_sheet_id', $answerSheet)
            ->whereNull('value')
            ->count();
    }

    public function questionCount($answerSheet)
    {
        return Answers::where('answer_sheet_id', $answerSheet)
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
            $isPassingScore = Setting::where('pass_score', '<=', $score);

            return $isPassingScore ? $approvedStatus : $declinedStatus;
        }

        return null;
    }


    public function calculatingAnswers($optionIDs, $answerSheet)
    {
        $correctAnswers = $this->correctAnswers($optionIDs);
        $falseAnswers = $this->falseAnswers($optionIDs);
        $nullAnswers = $this->nullAnswers($answerSheet);
        $questionCount = $this->questionCount($answerSheet);

        return [
            'correct' => $correctAnswers,
            'false' => $falseAnswers,
            'null' => $nullAnswers,
            'allQuestions' => $questionCount
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
