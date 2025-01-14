<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Traits\JobTrait;
use Modules\HRMS\app\Http\Traits\LevelTrait;
use Modules\HRMS\app\Http\Traits\PositionTrait;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\Position;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Resources\AllCoursesListResource;

class CourseOunitFeatureController extends Controller
{
    use LevelTrait , PositionTrait , JobTrait , CourseTrait;
    public function listing(Request $request)
    {




        $presentingCourse = $this->coursePresentingStatus()->id;

        $course = Course::whereHas('latestStatus', function ($query) use ($presentingCourse) {
            $query->where('statuses.id', $presentingCourse);
        })->get();



        $coursesResponse = AllCoursesListResource::collection($course);


        $data = [
            'courses' => $coursesResponse,
        ];

        return response() -> json($data);
    }
}
