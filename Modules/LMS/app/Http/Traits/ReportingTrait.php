<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\AnswerSheet;

trait ReportingTrait
{
    use AnswerSheetTrait;


    public function ans($answerSheetID, $student, $data, $courseID)
    {
        $answerSheets = AnswerSheet::joinRelationship('exam.courseExams.course')
            ->joinRelationship('status')
            ->select([
                'answer_sheets.start_date_time as startTime',
                'answer_sheets.student_id as studentID',
                'answer_sheets.status_id as statusID',
                'answer_sheets.score as score',
                'statuses.name as statusName',
                'answer_sheets.exam_id as examID',
                'answer_sheets.id as answerSheetID',
            ])
            ->where('courses.id', $courseID)
            ->where('answer_sheets.student_id', $student)
            ->where('answer_sheets.id', $answerSheetID)
            ->orderBy('answerSheetID', 'desc')
            ->get();


        $mostRecentAnswerSheet = $answerSheets->first();

        if (!$mostRecentAnswerSheet) {
            return response()->json(['error' => 'No answer sheets found'], 404);
        }

        $studentInfo = $this->student($student);
        $examId = $mostRecentAnswerSheet->examID;
        $examCount = $this->examCount($student, $courseID);
        $optionID = array_filter(array_column($data['questions'], 'option_id'));
        $startDate = $mostRecentAnswerSheet->startTime;
        $calculate = $this->counting($optionID, $answerSheetID, $examId);

        return [
            'calculate' => $calculate,
            'answerSheet' => $answerSheets,
            'studentInfo' => $studentInfo,
            'startDate' => $startDate,
            'ExamEnrollment' => $examCount,
        ];
    }


    public function counting($optionID, $answerSheetID, $examId)
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
        ];
    }

    public function examCount($student, $courseID)
    {
        $countExams = AnswerSheet::joinRelationship('exam.courseExams.course')
            ->joinRelationship('exam.courseExams')
            ->select([
                'answer_sheets.student_id as studentID',
                'answer_sheets.exam_id as examID',
                'courses.id as courseID',
            ])
            ->where('answer_sheets.student_id', $student)
            ->where('courses.id', $courseID)
            ->get()
            ->count();
        return $countExams;
    }
}
