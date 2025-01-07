<?php

namespace Modules\LMS\app\Http\Traits;

use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Exam;
use Modules\LMS\app\Models\Question;
use Modules\LMS\app\Models\Student;
use Modules\SettingsMS\app\Models\Setting;

trait ExamsTrait
{

    public function examsIndex(int $perPage = 10, int $pageNumber = 1, array $data = [], Student $auth,)
    {

        $query = AnswerSheet::joinRelationship('repository')
            ->joinRelationship('questionType', 'question_type_alias')
            ->joinRelationship('exam', 'exam_alias')
            ->joinRelationship('status');

        $query->addSelect([
            'answer_sheets.start_date_time as startDate',
            'answer_sheets.finish_date_time as finishDate',
            'repositories.name as repositoryName',
            'repositories.id as repositoryID',
            'exams.id as examID',
            'exams.title as examTitle',
            'question_types.id as questionTypeID',
            'statuses.name as statusName',
            'statuses.id as statusID'
        ])->where('answer_sheets.student_id', $auth->id);


        return $query->paginate($perPage, ['*'], 'page', $pageNumber);


    }

    public function examPreview($id)
    {
        $query = Exam::joinRelationship('courses');
        $query->leftJoinRelationship('questions');
        $query->addSelect([
            'exams.title as examTitle',
            'courses.title as courseTitle',
            'questions.title as questionTitle',
        ]);
        $query->withCount(['questions as totalQuestions']);

        return $query->where('exams.id', $id)->get();

    }

    public function createExam($course, $questionType, $repository)
    {


        $exam = Exam::create([
            'title' => $course->title,
            'questions_type_id' => $questionType->id,
            'repository_id' => $repository->id,
        ]);

        DB::table('course_exams')->insert([
            'exam_id' => $exam->id,
            'course_id' => $course->id,
        ]);

        $questionCountSetting = Setting::where('key', 'question_numbers_perExam')->first();
        $questionCount = $questionCountSetting ? $questionCountSetting->value : 5;

        $randomQuestions = Question::inRandomOrder()
            ->limit($questionCount)
            ->get();

        foreach ($randomQuestions as $question) {
            DB::table('question_exam')->insert([
                'exam_id' => $exam->id,
                'question_id' => $question->id,
            ]);
        }
        return $exam;


    }

    public function showExam($id)
    {
        $query = Exam::joinRelationship('questions.options');

        $query->addSelect([
            'questions.id as questionID',
            'questions.title as questionTitle',
            'options.id as optionID',
            'options.title as optionTitle'

        ]);
        return $query->where('exams.id', $id)->get();

    }


}
