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

        $levels = Level::where('status_id' , $this->activeLevelStatus()->id)->get();
        $positions = Position::with(['levels' => function ($query) {
            $query->where('status_id' , $this->activeLevelStatus()->id);
        }])->where('status_id' , $this->activePositionStatus()->id)->get();
        $jobs = Job::where('status_id' , $this->activeJobStatus()->id)->get();


        $presentingCourse = $this->coursePresentingStatus()->id;

        $course = Course::whereHas('latestStatus', function ($query) use ($presentingCourse) {
            $query->where('statuses.id', $presentingCourse);
        })->get();



        $coursesResponse = AllCoursesListResource::collection($course);


        $data = [
            'levels' => $levels ,
            'positions' => $positions,
            'jobs' => $jobs,
            'courses' => $coursesResponse,
        ];

        return response() -> json($data);
    }
}
