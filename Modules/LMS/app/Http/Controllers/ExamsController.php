<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Http\Enums\QuestionTypeEnum;
use Modules\LMS\app\Http\Enums\RepositoryEnum;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Http\Traits\ExamsTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Exam;
use Modules\LMS\app\Models\QuestionType;
use Modules\LMS\app\Models\Repository;
use Modules\LMS\app\Resources\ShowExamQuestionResource;

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
                $previewData = $this->previewExam($data->id, $courseId, $student);
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
            $student = Auth::user()->load('student');
            $examID = Exam::with('courses')->find($id);

            $courseID = $examID->courses->first()->id;

            $enrolled = $this->isEnrolledToDefinedCourse($courseID, $student);
            $completed = $this->isCourseCompleted($student);
            $attempted = $this->hasAttemptedAndPassedExam($student, $courseID);

            if ($enrolled && !$attempted && !$completed) {
                $examQuestions = $this->showExam($id);
                $response = new ShowExamQuestionResource($examQuestions);
                return response()->json([
                    'examQuestions' => $response
                ]);
            } else {
                return response()->json(['message' => 'شما اجازه دسترسی به سوالات این آزمون را ندارید'], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در دریافت سوالات و گزینه‌ها.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
