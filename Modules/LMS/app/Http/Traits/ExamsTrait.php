<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\QuestionsEnum;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\CourseExam;
use Modules\LMS\app\Models\Exam;
use Modules\LMS\app\Models\Question;
use Modules\LMS\app\Models\QuestionExam;
use Modules\LMS\app\Resources\ExamPreviewResource;
use Modules\SettingsMS\app\Models\Setting;

trait ExamsTrait
{

    use QuestionsTrait;

    public function examPreview($id)
    {
        $query = Exam::joinRelationship('courses');
        $query->leftJoinRelationship('questions');
        $query->addSelect([
            'exams.title as examTitle',
            'exams.id as examID',
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

        $questionExamData = $this->DataPreparation($exam, $course->id);

        QuestionExam::insert($questionExamData);

        return $exam;
    }


    public function DataPreparation($exam, $id)
    {
        $status = $this->questionActiveStatus()->id;

        $questionCountSetting = Setting::where('key', 'question_numbers_perExam')->first();
        $questionCount = $questionCountSetting ? $questionCountSetting->value : 5;

        $difficultySetting = Setting::where('key', 'Difficulty_for_exam')->first();
        $difficultyLevel = $difficultySetting ? $difficultySetting->value : null;

        $questionTypeSetting = Setting::where('key', 'question_type_for_exam')->first();
        $questionTypeLevel = $questionTypeSetting ? (int)$questionTypeSetting->value : null;

        $randomQuestions = Question::inRandomOrder()
            ->where('status_id', $status)
            ->when($difficultyLevel, function ($query) use ($difficultyLevel) {
                $query->where('difficulty_id', $difficultyLevel);
            })
            ->when($questionTypeLevel, function ($query) use ($questionTypeLevel) {
                $query->where('question_type_id', $questionTypeLevel);
            })
            ->joinRelationship('lesson.chapter.course')
            ->where('courses.id', $id)
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
            'options.title as optionTitle',
            'exams.id as exam_id'

        ]);
        return $query->where('exams.id', $id)->get();

    }

    public function PExam($examID, $courseID, $student)
    {
//        $enrolled = $this->isEnrolledToDefinedCourse($courseID, $student);
//        $completed = $this->isCourseCompleted($student);
//        $attempted = $this->hasAttemptedAndPassedExam($student, $courseID);
//        if ($enrolled && !$attempted && !$completed) {
        $exam = $this->examPreview($examID);
        $response = new ExamPreviewResource($exam);
        return $response;
//        } else {
//            return null;
//        }

    }


    public function examsIndex($student, array $data = [])
    {
        $query = AnswerSheet::joinRelationship('repository')
            ->joinRelationship('questionType', 'question_type_alias')
            ->joinRelationship('exam', 'exam_alias')
            ->joinRelationship('status');

        $query->addSelect([
            'answer_sheets.id as answerSheetID',
            'answer_sheets.start_date_time as startDate',
            'answer_sheets.finish_date_time as finishDate',
            'repositories.name as repositoryName',
            'repositories.id as repositoryID',
            'exams.id as examID',
            'exams.title as examTitle',
            'question_types.id as questionTypeID',
            'statuses.name as statusName',
            'statuses.id as statusID'
        ])->where('answer_sheets.student_id', $student->id);


        return $query->get();


    }
}
