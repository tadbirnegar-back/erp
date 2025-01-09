<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Traits\AnswerSheetTrait;
use Modules\LMS\app\Http\Traits\ExamResultTrait;
use Modules\LMS\app\Resources\ExamResultResource;

class ExamResultController extends Controller
{
    use ExamResultTrait, AnswerSheetTrait;

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


    public function storeAnsS(Request $req, $id)
    {
        $data = $req->all();

        if (!isset($data['questionInfos'])) {
            return response()->json(['error' => 'Question information is required'], 400);
        }
        $data['questions'] = json_decode($data['questionInfos'], true);

        $auth = User::with('student')->find(68);
        if (!$auth || !$auth->student) {
            return response()->json(['error' => 'User or student not found'], 404);
        }

        $student = $auth->student;

        try {
            $result = $this->StoringAnswerSheet($id, $student, $auth, $data);
//            return response()->json(new ExamResultResource($result));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to store answers', 'message' => $e->getMessage()], 500);
        }
    }


}
