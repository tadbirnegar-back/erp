<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\Answers;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Option;

trait ExamResultTrait
{

    public function examResult($auth, $id)
    {
//        $query = Answers::joinRelationship('answerSheet.exam.questions.options')

        $query = AnswerSheet::joinRelationship('answers.questions.options')

//            ->joinRelationship('exam.courseExams')
            ->joinRelationship('status');

        $query->where('answer_sheets.id', $id ?? null)
            ->where('answer_sheets.student_id', $auth->student->id);

        $query->addSelect([
//            'course_exams.course_id as courseID',
            'answer_sheets.score as answerSheetScore',
            'statuses.name as statusName',
            'answer_sheets.start_date_time as startDateTime',
            'answer_sheets.finish_date_time as finishDateTime',
            'options.id as optionID',
            'options.is_correct as isCorrect',
            'questions.id as questionID',
            'questions.title as questionTitle',
            'options.title as optionTitle',
        ]);

        return $this->counting($query);

    }

    public function counting($query)
    {

        $result = $query->get();

        $result = $result->map(function ($item) {


            $item->correct_answers_count = Answers::where('answer_sheet_id', $item->id)
                ->joinRelationship('options', function ($query) {
                    $query->where('answers.value', '=', 'options.title')
                        ->where('options.is_correct', 1);
                })
                ->count();


            $item->null_answers_count = Answers::where('answer_sheet_id', $item->id)
                ->joinRelationship('options', function ($query) {
                    $query->where('answers.value', '=', 'options.title')
                        ->whereNull('options.is_correct');
                })
                ->count();

            $item->questions_count = Answers::where('answer_sheet_id', $item->id)
                ->count('question_id');

            $item->false_answers_count = Answers::where('answer_sheet_id', $item->id)
                ->joinRelationship('options', function ($query) {
                    $query->where('options.is_correct', 0);
                })
                ->count();
            $item->false_answers_with_correct = Answers::where('answer_sheet_id', $item->id)
                ->join('options', 'answers.value', '=', 'options.title')
                ->where('options.is_correct', 0)
                ->get()
                ->map(function ($answer) {
                    $correctAnswer = Option::where('question_id', $answer->question_id)
                        ->where('is_correct', 1)
                        ->first();

                    $answer->correct_answer = $correctAnswer ? $correctAnswer->title : null;

                    return $answer;
                });

            $item->null_answers_with_correct = Answers::where('answer_sheet_id', $item->id)
                ->join('options', 'answers.value', '=', 'options.title')
                ->whereNull('options.is_correct')
                ->get()
                ->map(function ($answer) {
                    $correctAnswer = Option::where('question_id', $answer->question_id)
                        ->where('is_correct', 1)
                        ->first();

                    $answer->correct_answer = $correctAnswer ? $correctAnswer->title : null;

                    return $answer;
                });

            return $item;
        });

        return $result;
    }


}
