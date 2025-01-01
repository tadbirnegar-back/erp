<?php

namespace App\Http\Controllers;


use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\Gateway\app\Http\Traits\PaymentRepository;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\LMS\app\Http\Traits\ExamsTrait;
use Modules\LMS\app\Models\Exam;


class testController extends Controller
{
    use PaymentRepository, ApprovingListTrait, EnactmentTrait, MeetingMemberTrait, RecruitmentScriptTrait, MeetingTrait;
    use ExamsTrait;

    public function run()
    {

        $query = Exam::joinRelationship('course');
        $query->leftJoinRelationship('questions');
        $query->addSelect([
            'exams.title as examTitle',
            'courses.title as coursesTitle',
            'questions.title as question_title',
        ]);
        $query->withCount(['questions as totalQuestions']);

        return $query->where('exams.id', 1)->get();


    }
}
