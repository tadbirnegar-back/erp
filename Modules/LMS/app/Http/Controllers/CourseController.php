<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Models\Course;

class CourseController extends Controller
{
    use CourseTrait;
    public function show($id)
    {
        try {
            $course = Course::with('latestStatus')->findOrFail($id);
            $user = Auth::user();

            if (is_null($course)) {
                return response()->json(['message' => 'دوره مورد نظر یافت نشد'], 404);
            }

            $componentsToRenderWithData = $this -> courseShow($course , $user);
            return response()->json($componentsToRenderWithData);
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

}
