<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Http\Traits\ExamsTrait;
use Modules\LMS\app\Models\Exam;
use Modules\LMS\app\Resources\ExamsResultResource;

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
        $response = ExamsResultResource::make($result);
        return $response;

    }

    public function previewExam($id)
    {
        DB::beginTransaction();
        $student = User::with('student')->find(68);
        $examID = Exam::with('courses')->find($id);
        $courseID = $examID->courses->first()->id;
        $enrolled = $this->isEnrolledToDefinedCourse($courseID, $student);
        $completed = $this->isCourseCompleted($student);
        $attempted = $this->isAttemptedExam($student, $id);
        $passed = $this->isPassed($student);
        return response()->json([
            'enrolled' => $enrolled,
            'completed' => $completed,
            'passed' => $passed,
            'attempted' => $attempted
        ]);


        try {
            if ($enrolled && !$completed && !$passed && $attempted) {
                $exam = $this->examDetails();
                $response = ExamsResultResource::make($exam);
                DB::commit();
                return response()->json($response);
            } else {
//                dd($enrolled, $completed, $passed);

                DB::rollBack();
                return response()->json(['message' => 'شما اجازه دسترسی به این آزمون را ندارید'], 403);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([$e, 'message' => 'خطایی رخ داده است'], 500);
        }


    }

}
