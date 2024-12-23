<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $auth = Auth::user();
        $auth->load('student');
        $student = $auth->student;
        $data = $request->all();

        $perPage = $data['perPage'] ?? 10;
        $pageNumber = $data['pageNumber'] ?? 1;

        $result = $this->examsIndex($perPage, $pageNumber, $data, $student);
        $response = ExamsResource::make($result);
        return $response;

    }

}
