<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Models\Lesson;

class LessonController extends Controller
{
    public function show($id): JsonResponse
    {
        $lesson = Lesson::find($id);
        return response()->json($lesson);
    }
}
