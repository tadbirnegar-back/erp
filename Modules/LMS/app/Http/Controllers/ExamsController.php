<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Http\Enums\QuestionTypeEnum;
use Modules\LMS\app\Http\Enums\RepositoryEnum;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Http\Traits\ExamsTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Exam;
use Modules\LMS\app\Models\QuestionExam;
use Modules\LMS\app\Models\QuestionType;
use Modules\LMS\app\Models\Repository;
use Modules\LMS\app\Resources\ExamListResource;
use Modules\LMS\app\Resources\ShowExamQuestionResource;
use Modules\SettingsMS\app\Models\Setting;

class ExamsController extends Controller
{
    use ExamsTrait, CourseTrait;

    /**
     * Display a listing of the resource.
     */
    public function generateExam($courseId)
    {
        try {
            DB::beginTransaction();

            $course = Course::find($courseId);
            if (empty($course)) {
                return response()->json(['message' => 'دوره‌ای با این شناسه یافت نشد.'], 404);
            }


            $student = Auth::user()->load('student');
            $enrolled = $this->isEnrolledToDefinedCourse($courseId, $student);
            $completed = $this->isCourseCompleted($student);
            $attempted = $this->hasAttemptedAndPassedExam($student, $courseId);
            if ($enrolled && !$attempted && !$completed) {

                $questionType = QuestionType::where('name', QuestionTypeEnum::MULTIPLE_CHOICE_QUESTIONS->value)->firstOrFail();
                $repository = Repository::where('name', RepositoryEnum::FINAL->value)->firstOrFail();
                $data = $this->createExam($course, $questionType, $repository);
                $previewData = $this->PExam($data->id, $courseId, $student);
                DB::commit();
                return response()->json($previewData);
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
                ], 422);
            }

            $enrolled = $this->isEnrolledToDefinedCourse($courseID, $student);
            $completed = $this->isCourseCompleted($student);
            $attempted = $this->hasAttemptedAndPassedExam($student, $courseID);

            if ($enrolled && !$attempted && !$completed) {
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

        $student = Auth::user()->load('student');
        $data = $request->all();

        $result = $this->examsIndex($student->student, $data);
        $response = ExamListResource::make($result);
        return response()->json($response);

    }

}
