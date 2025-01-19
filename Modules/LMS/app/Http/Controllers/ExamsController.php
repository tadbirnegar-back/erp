<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Traits\ExamsTrait;
use Modules\LMS\app\Resources\ExamsResource;

class ExamsController extends Controller
{
    use ExamsTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
//        $auth = Auth::user();
        $auth = User::with('student')->find(68);
        $auth->load('student');
        $student = $auth->student;
        $data = $request->all();

        $result = $this->examsIndex($data, $student);
        return response()->json($result);
        $response = ExamsResource::make($result);
        return $response;

    }

}
