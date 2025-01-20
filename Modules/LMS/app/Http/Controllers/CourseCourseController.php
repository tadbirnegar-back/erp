<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Resources\AllCoursesListResource;

class CourseCourseController extends Controller
{
    use CourseTrait;
    public function listing($id)
    {
        $presentingCourse = $this->coursePresentingStatus()->id;

        $course = Course::whereHas('latestStatus', function ($query) use ($presentingCourse) {
            $query->where('statuses.id', $presentingCourse);
        })->whereNot('id' , $id)->get();
        $coursesResponse = AllCoursesListResource::collection($course);
        $data = [
            'courses' => $coursesResponse,
        ];
        return response() -> json($data);
    }
}
