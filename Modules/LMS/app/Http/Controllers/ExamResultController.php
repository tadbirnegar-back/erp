<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Traits\ExamResultTrait;
use Modules\LMS\app\Resources\ExamResultResource;

class ExamResultController extends Controller
{
    use ExamResultTrait;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function result($id): JsonResponse
    {

//        $auth = Auth::user();
//        $auth->load('student');
        $auth = User::find(68);
        $auth->load('student');
        $result = $this->examResult($auth, $id);
//        dd($result);
        $response = new ExamResultResource($result);
        return response()->json($response);

    }


    public function storeAnsS()
    {

    }


}
