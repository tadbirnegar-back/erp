<?php

namespace Modules\LMS\app\Http\Traits;

use Carbon\Carbon;
use Modules\LMS\app\Models\Answers;
use Modules\LMS\app\Models\AnswerSheet;

trait ExamResultTrait
{
    public function result(array $data = [],)
    {
        $query = AnswerSheet::joinRelationship('answers.questions.options')
            ->joinRelationship('status')
            ->joinRelationship('student.customer.person')//            ->joinRelationship('student.customer.avatar')
        ;
        $query->addSelect([
            'answer_sheets.score as answer_sheet_score',
            'statuses.name as status_name',
            'answer_sheets.start_date_time as start_date_time',
            'answer_sheets.finish_date_time as finish_date_time',
            'options.id as option_id',
            'options.is_correct as is_correct',
            'questions.id as question_id',
            'persons.display_name as student_name',
            'persons.id as student_id',
//                'avatars.slug as student_avatar',
        ]);

        if (isset($data['exam_id'])) {
            $query->where('answer_sheets.exam_id', $data['exam_id']);
        }

        $results = $query->get();

        foreach ($results as $result) {
            $start = Carbon::parse($result->start_date_time);
            $finish = Carbon::parse($result->finish_date_time);

            $usedTimeInMinutes = $start->diffInMinutes($finish);

            $result->used_time_in_minutes = $usedTimeInMinutes;


            $correctAnswersCount = Answers::where('answer_sheet_id', $result->id)
                ->whereHas('options', function ($query) {
                    $query->where('is_correct', 1);
                })
                ->count();


            $result->correct_answers_count = $correctAnswersCount;

            $NullAnswersCount = Answers::where('answer_sheet_id', $result->id)
                ->whereHas('options', function ($query) {
                    $query->whereNull('is_correct');
                })
                ->count();
            
            $result->null_answers_count = $NullAnswersCount;


            $questionsCount = Answers::where('answer_sheet_id', $result->id)
                ->count('question_id');

            $result->questions_count = $questionsCount;


            $falseAnswersCount = Answers::where('answer_sheet_id', $result->id)
                ->whereHas('options', function ($query) {
                    $query->where('options.is_correct', 0);
                })
                ->count();

            $result->false_answers_count = $falseAnswersCount;


        }
        return $results;

    }


    public function detailResult(array $data = [],)
    {
        $query = AnswerSheet::joinRelationship('answers.questions.options')
            ->joinRelationship('status')
            ->joinRelationship('student.customer.person')//->joinRelationship('student.customer.avatar')
        ;
        $query->addSelect([
            'answer_sheets.score as answer_sheet_score',
            'statuses.name as status_name',
            'answer_sheets.start_date_time as start_date_time',
            'answer_sheets.finish_date_time as finish_date_time',
            'options.id as option_id',
            'options.is_correct as is_correct',
            'questions.id as question_id',
            'persons.display_name as student_name',
            'persons.id as student_id',
            'questions.title as question_title',
            'options.title as option_title',

        ]);

        if (isset($data['exam_id'])) {
            $query->where('answer_sheets.exam_id', $data['exam_id']);
        }

        $results = $query->get();

        foreach ($results as $result) {
            $start = Carbon::parse($result->start_date_time);
            $finish = Carbon::parse($result->finish_date_time);

            $usedTimeInMinutes = $start->diffInMinutes($finish);

            $result->used_time_in_minutes = $usedTimeInMinutes;


            $correctAnswersCount = Answers::where('answer_sheet_id', $result->id)
                ->whereHas('options', function ($query) {
                    $query->where('is_correct', 1);
                })
                ->count();


            $result->correct_answers_count = $correctAnswersCount;

            $NullAnswersCount = Answers::where('answer_sheet_id', $result->id)
                ->join('options', 'answers.value', '=', 'options.id')
                ->where('options.is_correct', null)
                ->count();

            $result->null_answers_count = $NullAnswersCount;


            $questionsCount = Answers::where('answer_sheet_id', $result->id)
                ->count('question_id');

            $result->questions_count = $questionsCount;


            $falseAnswersCount = Answers::where('answer_sheet_id', $result->id)
                ->whereHas('options', function ($query) {
                    $query->where('is_correct', 0);
                })
                ->count();

            $result->false_answers_count = $falseAnswersCount;


        }
        return $results;


    }

}
