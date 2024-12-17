<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Resources\LessonListResource;
use Modules\LMS\app\Resources\CourseListResource;

class CourseController extends Controller
{
    use CourseTrait;

    public function show($id)
    {
        try {
            DB::beginTransaction();
            $course = Course::with('latestStatus')->findOrFail($id);
            $user = Auth::user();
            if (is_null($course)) {
                return response()->json(['message' => 'دوره مورد نظر یافت نشد'], 404);
            }

            $componentsToRenderWithData = $this->courseShow($course, $user);
            DB::commit();
            return response()->json($componentsToRenderWithData);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function courseList(Request $request)
    {
        $data = $request->all();
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $result = $this->courseIndex($perPage, $pageNum, $data);

        $response = new CourseListResource($result);

        return $response;
    }

    public function lesson_index(Request $request)
    {


        $data = $request->all();
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $result = $this->lessonIndex($perPage, $pageNum, $data);
        $response = new LessonListResource($result);

        return $response;


    }

}
