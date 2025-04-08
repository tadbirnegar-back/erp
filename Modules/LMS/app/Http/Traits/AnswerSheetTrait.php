<?php

namespace Modules\LMS\app\Http\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Modules\AAA\app\Models\User;
use Modules\FormGMS\app\Models\Option;
use Modules\LMS\app\Http\Enums\AnswerSheetStatusEnum;
use Modules\LMS\app\Models\Answers;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\QuestionExam;
use Modules\SettingsMS\app\Models\Setting;


trait AnswerSheetTrait
{

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

                $value = $option->title;
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
        $answerSheetID = $answerSheet->id;

        $final = $this->final($answerSheet);
        $calculate = $this->calculatingAnswers($optionID, $answerSheetID, $usedTime, $examId);
        $studentInfo = $this->student($student);

        return [
            'answerSheet' => $answerSheet,
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
            $passScore = Setting::where('key', 'pass_score')->value('value');

            return ($passScore !== null && $score >= $passScore) ? $approvedStatus : $declinedStatus;
        }

        return null;
    }


    public function calculatingAnswers($optionID, $answerSheetID, $usedTime, $examId)
    {
        $correctAnswers = $this->correctAnswers($optionID);
        $falseAnswers = $this->falseAnswers($optionID);
        $nullAnswers = $this->nullAnswers($answerSheetID);
        $score = $this->score($examId, $optionID);
        $questionCount = $this->questionCount($examId);

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
        $query = User::query()
            ->leftJoinRelationship('person.avatar')
            ->leftJoinRelationship('roles.RolePosition.position')
            ->select([
                'persons.display_name as name',
                'files.slug as avatar',
                'positions.name as poseName'
            ])
            ->where('users.id', $student->id)
            ->first();

        return [
            'name' => $query->name ?? null,
            'avatar' => $query->avatar ?? null,
            'poseName' => $query->poseName ?? null,
        ];
    }


    public function Show($answerSheetID, $student, $data)
    {
        $answerSheets = AnswerSheet::joinRelationship('answers.questions.options', [
            'answers' => fn($join) => $join->as('answers_alias'),
            'questions' => fn($join) => $join->as('questions_alias'),
            'options' => fn($join) => $join->as('options_alias')
        ])
            ->joinRelationship('status')
            ->select([
                'answer_sheets.id as answerSheetID',
                'answer_sheets.exam_id as examID',
                'answer_sheets.start_date_time as startTime',
                'answer_sheets.finish_date_time as finishTime',
                'answer_sheets.student_id as studentID',
                'answer_sheets.status_id as statusID',
                'options_alias.id as optionID',
                'options_alias.is_correct as isCorrect',
                'questions_alias.id as questionID',
                'questions_alias.title as questionTitle',
                'options_alias.title as optionTitle',
                'answer_sheets.score as score',
            ])
            ->where('answer_sheets.id', $answerSheetID)
            ->get();

        if ($answerSheets->isEmpty()) {
            throw new Exception('Answer Sheets not found.');
        }

        $status = AnswerSheet::joinRelationship('answers.questions.options', [
            'answers' => fn($join) => $join->as('answers_alias'),
            'questions' => fn($join) => $join->as('questions_alias'),
            'options' => fn($join) => $join->as('options_alias')
        ])
            ->joinRelationship('status')
            ->select([
                'statuses.name as statusName',
            ])
            ->where('answer_sheets.id', $answerSheetID)
            ->first();

        $studentInfo = $this->student($student);
        foreach ($answerSheets as $sheet) {
            $usedTime = $this->calculateUsedTime($sheet);
        }

        $examId = $answerSheets->first()->examID;
        $userAns = $this->getUserAnswers($data);
        $optionID = array_filter(array_column($data['questions'], 'option_id'));
        $startDate = $answerSheets->first()->startTime;
        $courseID = $this->getCourseID($answerSheetID);

        $calculate = $this->calculatingAnswers($optionID, $answerSheetID, $usedTime, $examId);


        return [
            'calculate' => $calculate,
            'answerSheet' => $answerSheets,
            'studentInfo' => $studentInfo,
            'usedTime' => $usedTime,
            'startDate' => $startDate,
            'status' => $status,
            'userAnswer' => $userAns,
            'courseID' => $courseID->courseID
        ];
    }


    private function calculateUsedTime($answerSheet)
    {

        $startTime = $answerSheet->startTime;
        $finishTime = $answerSheet->finishTime;

        if ($startTime && $finishTime) {
            return strtotime($finishTime) - strtotime($startTime);
        }

        return null;
    }

    public function getUserAnswers($data)
    {
        collect($data['questions'])
            ->mapWithKeys(function ($question) {
                return [$question['question_id'] => $question['option_id']];
            })
            ->filter(function ($optionID, $questionID) {
                return $questionID !== null && $optionID !== null;
            })
            ->toArray();

        return $data;
    }

    public function getCourseID($answerSheetID)
    {
        return AnswerSheet::query()
            ->leftJoinRelationship('exam.courseExams.course', [
                'course' => fn($join) => $join->as('course_alias'),
            ])
            ->select('course_alias.id as courseID')
            ->where('answer_sheets.id', $answerSheetID)->first();

    }

    public function answerSheetApprovedStatus()
    {
        return Cache::rememberForever('answer_sheet_approved_status', function () {
            return AnswerSheet::GetAllStatuses()
                ->firstWhere('name', AnswerSheetStatusEnum::APPROVED->value);
        });
    }

    public function answerSheetTakingExamStatus()
    {
        return Cache::rememberForever('answer_sheet_taking_exam_status', function () {
            return AnswerSheet::GetAllStatuses()
                ->firstWhere('name', AnswerSheetStatusEnum::TAKING_EXAM->value);
        });
    }

    public function answerSheetDeclinedStatus()
    {
        return Cache::rememberForever('answer_sheet_declined_status', function () {
            return AnswerSheet::GetAllStatuses()
                ->firstWhere('name', AnswerSheetStatusEnum::DECLINED->value);
        });
    }


}
