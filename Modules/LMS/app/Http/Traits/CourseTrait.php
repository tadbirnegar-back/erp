<?php

namespace Modules\LMS\app\Http\Traits;

use Illuminate\Support\Facades\Auth;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Enums\CourseStatusEnum;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Enroll;

trait CourseTrait
{
    use AnswerSheetTrait;
    public function courseShow($course, $user)
    {

        $user->load([
            'enrolls' => function ($query) use ($course) {
                $query->where('course_id', $course->id);
            },
            'answerSheets'
        ]);
        $enrolls = $user->enrolls[0];
        $answerSheet = $user->answerSheets[0];



        $exampApprovedStatus =  $this -> answerSheetApprovedStatus() -> id;

        if($answerSheet -> status_id == $exampApprovedStatus) {
            $approveFromExam = true;
        }else{
            $approveFromExam = false;
        }

        if (empty($enrolls)) {
            $joined = false;
        } else {
            $joined = true;
        }

        $status = $course->load('latestStatus');
        $statusName = $status->latestStatus[0]->name;

        $permissions = $this::getComponentToRenderLMS($joined, $statusName,$approveFromExam);
        return $permissions;
    }


    private static function getComponentToRenderLMS($joined, $status , $approveFromExam)
    {
        return "hi";
    }



    public function coursePresentingStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::PRESENTING->value);
    }

    public function courseCanceledStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::CANCELED->value);
    }

    public function courseEndedStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::ENDED->value);
    }
}
