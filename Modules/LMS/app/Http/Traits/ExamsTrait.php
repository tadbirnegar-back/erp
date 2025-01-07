<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\CourseExam;
use Modules\LMS\app\Models\Exam;
use Modules\LMS\app\Models\Question;
use Modules\LMS\app\Models\QuestionExam;
use Modules\SettingsMS\app\Models\Setting;

trait ExamsTrait
{


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

        CourseExam::insert([
            'exam_id' => $exam->id,
            'course_id' => $course->id,
        ]);

        $questionExamData = $this->DataPreparation($exam);

        QuestionExam::insert($questionExamData);

        return $exam;
    }


    public function DataPreparation($exam)
    {

        $questionCountSetting = Setting::where('key', 'question_numbers_perExam')->first();
        $questionCount = $questionCountSetting ? $questionCountSetting->value : 5;

        $randomQuestions = Question::inRandomOrder()
            ->limit($questionCount)
            ->get();
        $data = $randomQuestions->map(function ($question) use ($exam) {
            return [
                'exam_id' => $exam->id,
                'question_id' => $question->id,
            ];
        })->toArray();
        return $data;

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
