<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Enums\QuestionTypeEnum;
use Modules\LMS\app\Http\Enums\RepositoryEnum;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Http\Traits\ExamsTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Exam;
use Modules\LMS\app\Models\QuestionType;
use Modules\LMS\app\Models\Repository;
use Modules\LMS\app\Resources\ExamPreviewResource;
use Modules\LMS\app\Resources\ExamsResultResource;
use Modules\LMS\app\Resources\ShowExamQuestionResource;

class ExamsController extends Controller
{
    use ExamsTrait, CourseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $auth = Auth::user();
        $auth->load('student');

        $student = $auth->student;
        $data = $request->all();

        $perPage = $data['perPage'] ?? 10;
        $pageNumber = $data['pageNumber'] ?? 1;

        $result = $this->examsIndex($perPage, $pageNumber, $data, $student);
        $response = new ExamsResultResource($result);
        return $response;

    }

    public function previewExam($id)
    {
        DB::beginTransaction();
        $student = User::with('student')->find(68);
        $examID = Exam::with('courses')->find($id);

//        $student = Auth::user()->load('student');
//        $examID = Exam::with('courses')->find($id);
        $courseID = $examID->courses->first()->id;
        $enrolled = $this->isEnrolledToDefinedCourse($courseID, $student);
        $completed = $this->isCourseCompleted($student);
        $attempted = $this->isAttemptedExam($student, $id);
        $passed = $this->isPassed($student);

        try {
            if ($enrolled && $passed && !$attempted && !$completed) {
                $exam = $this->examPreview($id);
                $response = new ExamPreviewResource($exam);
                DB::commit();
                return response()->json($response);
            } else {

                DB::rollBack();
                return response()->json(['message' => 'شما اجازه دسترسی به این آزمون را ندارید'], 403);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([$e, 'message' => 'خطایی رخ داده است'], 500);
        }


    }


    public function generateExam($id)
    {
        try {
            DB::beginTransaction();
            $course = Course::findOrFail($id);

            $questionType = QuestionType::where('name', QuestionTypeEnum::MULTIPLE_CHOICE_QUESTIONS->value)->firstOrFail();
            $repository = Repository::where('name', RepositoryEnum::FINAL->value)->firstOrFail();

            $exam = $this->createExam($course, $questionType, $repository);


            return response()->json($exam);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'خطایی در ایجاد آزمون رخ داد.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }


    public function showExamQuestions($id)
    {
        try {
            $examQuestions = $this->showExam($id);
            $response = new ShowExamQuestionResource($examQuestions);
            return response()->json([
                'examQuestions' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در دریافت سوالات و گزینه‌ها.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
