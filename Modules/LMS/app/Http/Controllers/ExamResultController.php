<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\LMS\app\Http\Traits\ExamResultTrait;
use Modules\LMS\app\Resources\ExamResultDetailResource;
use Modules\LMS\app\Resources\ExamResultResource;

class ExamResultController extends Controller
{
    use ExamResultTrait;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $auth = Auth::user();
        $auth->load('student');
        $data = $request->all();
        $result = $this->result($data);
        $response = new ExamResultResource($result);
        return response()->json($response);

    }

    public function detailShow(Request $request): JsonResponse
    {
        $auth = Auth::user();
        $auth->load('student');
        $data = $request->all();
        $result = $this->detailResult($data);
        $response = new ExamResultDetailResource($result);
        return response()->json($response);
    }

}
