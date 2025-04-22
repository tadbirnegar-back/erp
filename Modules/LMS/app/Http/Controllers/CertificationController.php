<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\LMS\app\Http\Traits\AnswerSheetTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Enroll;
use Modules\LMS\app\Resources\CertificationsListResource;

class CertificationController extends Controller
{
    use RecruitmentScriptTrait, AnswerSheetTrait;

    public function listOfCertification(Request $request)
    {

        $data = $request->all();
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;
        $approvedAnswerSheetStatus = $this->answerSheetApprovedStatus();
        $query =Enroll::join('courses', 'enrolls.course_id', '=', 'courses.id')
            ->where('enrolls.certificate_file_id', null)
            ->join('orders', 'enrolls.id', '=', 'orders.orderable_id')
            ->where('orders.orderable_type', '=', Enroll::class)
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('course_exam', 'course_exam.course_id', '=', 'courses.id')
            ->join('exams', 'exams.id', '=', 'course_exam.exam_id')
            ->join('answer_sheets', function ($join) {
                $join->on('answer_sheets.exam_id', '=', 'exams.id')
                    ->on('answer_sheets.student_id', '=', 'customers.customerable_id');
            })
            ->where('answer_sheets.status_id', $approvedAnswerSheetStatus->id)
            ->where('enrolls.first_completed_date' , '!=' , null)

            ->select([
                'courses.id as course_id',
                'courses.title as course_title',
                'answer_sheets.score as score',
                'enrolls.id as enroll_id',
                'answer_sheets.student_id as student_id',
            ])
            ->distinct()
            ->paginate($perPage, ['*'], 'page', $pageNum);



        return CertificationsListResource::collection($query);
    }

}
