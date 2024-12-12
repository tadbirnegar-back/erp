<?php

namespace Modules\LMS\app\Http\Traits;

use Illuminate\Support\Facades\Auth;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Enums\CourseStatusEnum;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Enroll;

trait CourseTrait
{
    public function courseShow($course, $user)
    {

        $user->load(['enrolls' => function ($query) use ($course) {
            $query->where('course_id', $course->id);
        }]);

        $enrolls = $user->enrolls[0];


        if (empty($enrolls)) {
            $joined = false;
        } else {
            $joined = true;
        }

        $status = $course->load('latestStatus');
        $permissions = $this::getComponentToRenderLMS($joined, $status->latestStatus[0]->name);
    }


    private static function getComponentToRenderLMS($joined, $status)
    {

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
