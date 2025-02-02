<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\LMS\app\Http\Enums\CourseStatusEnum;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\StatusCourse;
use Modules\LMS\app\Resources\AllCoursesListResource;

class CourseCourseController extends Controller
{
    use CourseTrait;

    public function listing($id)
    {

        $course = Course::where('id', '!=', $id)
            ->with('status')
            ->get()
            ->filter(function ($course) {
                return $course->status->name == CourseStatusEnum::PRESENTING->value;
            })->values();



        $coursesResponse = AllCoursesListResource::collection($course);
        $data = [
            'courses' => $coursesResponse,
        ];
        return response()->json($data);
    }
}
