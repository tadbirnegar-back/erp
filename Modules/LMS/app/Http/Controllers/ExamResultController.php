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


    public function storeAnsS(Request $request, $examId)
    {
        // دریافت داده به صورت JSON
        $jsonData = $request->input('data');

        $data = json_decode($jsonData, true);

        if (!$data) {
            return response()->json(['error' => 'Invalid JSON format'], 400);
        }

        $optionId = $data['optionId'];
        $student = User::with('student')->find(68);

        $answerSheet = $this->storeAnswerSheet($examId, $student, $optionId, $data);

        return response()->json([
            'message' => 'Answer sheet stored successfully.',
            'answerSheet' => $answerSheet
        ], 200);
    }


}
