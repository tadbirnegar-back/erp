<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Enums\CourseTypeEnum;
use Modules\LMS\app\Http\Enums\QuestionsEnum;
use Modules\LMS\app\Http\Enums\QuestionTypeEnum;
use Modules\LMS\app\Http\Enums\RepositoryEnum;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Http\Traits\ExamsTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Exam;
use Modules\LMS\app\Models\Question;
use Modules\LMS\app\Models\QuestionExam;
use Modules\LMS\app\Models\QuestionType;
use Modules\LMS\app\Models\Repository;
use Modules\LMS\app\Resources\ExamListResource;
use Modules\LMS\app\Resources\ShowExamQuestionResource;
use Modules\SettingsMS\app\Models\Setting;
use Modules\StatusMS\app\Models\Status;

class ExamsController extends Controller
{
    use ExamsTrait, CourseTrait;

    /**
     * Display a listing of the resource.
     */
    public function previewExam($courseId)
    {
        try {
            DB::beginTransaction();

            $course = Course::find($courseId);
            if (empty($course)) {
                return response()->json(['message' => 'دوره‌ای با این شناسه یافت نشد.'], 404);
            }


            $student = Auth::user()->load('student');
            $enrolled = $this->isEnrolledToDefinedCourse($courseId, $student);
            if(is_null($enrolled)){
                $isEnrolles = false;
            }else{
                $isEnrolles = true;
            }
            $completed = $this->isCourseNotCompleted($student);
            if($course->course_type['value'] == CourseTypeEnum::MOKATEBEYI->value) {
                $attempted = $this->hasAttemptToExam($student->student, $course->id);
            }else{
                $attempted = $this->hasAttemptedAndPassedExam($student->student, $course->id);
            }
            if ($isEnrolles && $completed && !$attempted) {
                $course = Course::find($courseId);
                if($course->course_type['value'] == CourseTypeEnum::MOKATEBEYI->value) {
                    $settings = Setting::whereIn('key', [
                        'question_numbers_perExam_comprehensive',
                        'time_per_questions_comprehensive',
                    ])->pluck('value', 'key');
                    $timePerQuestions = $settings->get('time_per_questions_comprehensive');
                    $questionNumber = $settings->get('question_numbers_perExam_comprehensive');
                    $examTime = $timePerQuestions * $questionNumber * 60;
                    $courseTitle = $course->title;
                }else{
                    $settings = Setting::whereIn('key', [
                        'question_numbers_perExam',
                        'time_per_questions',
                    ])->pluck('value', 'key');
                    $timePerQuestions = $settings->get('time_per_questions');
                    $questionNumber = $settings->get('question_numbers_perExam');
                    $examTime = $timePerQuestions * $questionNumber * 60;
                    $courseTitle = $course->title;
                }
                DB::commit();
                return response()->json([
                    'course_id' => $course->id,
                    'course_title' => $courseTitle,
                    'timePerQuestion' => $timePerQuestions . ':0',
                    'exam_time' => $examTime,
                    'questionsCount' => $questionNumber,
                    'exam_type' => $course->course_type['value'] == CourseTypeEnum::MOKATEBEYI->value ? 'آزمون مکاتبه ای' : 'آزمون نهایی'
                ]);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'شما اجازه دسترسی به این آزمون را ندارید'], 403);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطایی در ایجاد آزمون رخ داد.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }


    public function generateExam($id)
    {
        try {
            DB::beginTransaction();

            $course = Course::find($id);
            if (empty($course)) {
                return response()->json(['message' => 'دوره‌ای با این شناسه یافت نشد.'], 404);
            }


            $student = Auth::user()->load('student');
            $enrolled = $this->isEnrolledToDefinedCourse($id, $student);
            $completed = $this->isCourseNotCompleted($student);
            if($course->course_type['value'] == CourseTypeEnum::MOKATEBEYI->value) {
                $attempted = $this->hasAttemptToExam($student->student, $id);
            }else{
                $attempted = $this->hasAttemptedAndPassedExam($student->student, $id);
            }
            if ($enrolled && $completed && !$attempted) {
                $questionType = QuestionType::where('name', QuestionTypeEnum::MULTIPLE_CHOICE_QUESTIONS->value)->firstOrFail();
                $repository = Repository::where('name', RepositoryEnum::FINAL->value)->firstOrFail();
                $data = $this->createExam($course, $questionType, $repository);

                //show exam
                $exam = Exam::with('courses')->find($data->id);
                if (!$exam) {
                    return response()->json(['error' => 'آزمون یافت نشد.'], 404);
                }

                $courseID = $exam->courses->first()->id ?? null;
                if (!$courseID) {
                    return response()->json(['error' => 'دوره‌ای برای این آزمون یافت نشد.'], 404);
                }

                $questionCount = QuestionExam::where('exam_id', $exam->id)->count();
                $questionLimit = (int)Setting::where('key', 'question_numbers_perExam')->value('value');
                if ($questionCount < $questionLimit) {
                    return response()->json([
                        'error' => 'تعداد سوالات آزمون با مقدار تعیین شده در تنظیمات همخوانی ندارد.'
                    ], 404);
                }

                $examQuestions = $this->showExam($exam->id);
                $response = new ShowExamQuestionResource($examQuestions);

                DB::commit();

                return response()->json([
                    'examQuestions' => $response
                ]);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'شما اجازه دسترسی به این آزمون را ندارید'], 403);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطایی در ایجاد آزمون رخ داد.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }


    public function showExamQuestions($id)
    {
        try {
            DB::beginTransaction();

            $student = Auth::user()->load('student');
            if (!$student) {
                return response()->json(['error' => 'کاربر یافت نشد.'], 404);
            }

            $exam = Exam::with('courses')->find($id);
            if (!$exam) {
                return response()->json(['error' => 'آزمون یافت نشد.'], 404);
            }

            $courseID = $exam->courses->first()->id ?? null;
            if (!$courseID) {
                return response()->json(['error' => 'دوره‌ای برای این آزمون یافت نشد.'], 404);
            }

            $questionCount = QuestionExam::where('exam_id', $exam->id)->count();
            $questionLimit = (int)Setting::where('key', 'question_numbers_perExam')->value('value');
            if ($questionCount < $questionLimit) {
                return response()->json([
                    'error' => 'تعداد سوالات آزمون با مقدار تعیین شده در تنظیمات همخوانی ندارد.'
                ], 403);
            }

            $enrolled = $this->isEnrolledToDefinedCourse($courseID, $student);
            $completed = $this->isCourseNotCompleted($student);
            if($exam->course->first()->course_type['value'] == CourseTypeEnum::MOKATEBEYI->value) {
                $attempted = $this->hasAttemptToExam($student->student, $id);
            }else{
                $attempted = $this->hasAttemptedAndPassedExam($student->student, $id);
            }
            if ($enrolled && !$attempted && $completed) {
                $examQuestions = $this->showExam($id);
                $response = new ShowExamQuestionResource($examQuestions);

                DB::commit();

                return response()->json([
                    'examQuestions' => $response
                ]);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'شما اجازه دسترسی به سوالات این آزمون را ندارید'], 403);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطا در دریافت سوالات و گزینه‌ها.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {

        $student = Auth::user();
        $student->load('student');
        $data = $request->all();

        $result = $this->examsIndex($student->student, $data);
        $response = ExamListResource::make($result);
        return response()->json($response);

    }

    public function isExamReady($id)
    {
        $settings = Setting::whereIn('key', [
            'question_numbers_perExam',
            'Difficulty_for_exam',
            'question_type_for_exam'
        ])->pluck('value', 'key');
        $statusID = Status::where('model', Question::class)->where('name', QuestionsEnum::ACTIVE->value)->first();

        $repo = Repository::where('name', RepositoryEnum::FINAL->value)->first();

        if (!$repo) {
            return response()->json(['situation' => false], 204);
        }

        $questionsNumbersExist = Course::withCount(['questions' => function ($query) use ($settings, $statusID, $repo) {
            $query->where('question_type_id', $settings->get('question_type_for_exam'));
            $query->where('difficulty_id', $settings->get('Difficulty_for_exam'));
            $query->where('status_id', $statusID->id);
            $query->where('repository_id', $repo->id);
        }])->find($id);

        if ($questionsNumbersExist->questions_count >= $settings->get('question_numbers_perExam')) {
            return response()->json(['situation' => true], 200);
        } else {
            return response()->json(['situation' => false], 204);
        }
    }

}
