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
        if (!$examId) {
            return response()->json(['message' => 'Exam ID is missing or null'], 400);
        }
        $student = User::with('student')->find(68);
        if (!$student) {
            return response()->json(['message' => 'Student not found.'], 404);
        }
//        return response()->json($student);
        $questionInfos = json_decode($request->questionInfos, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'message' => 'Invalid JSON in questionInfos.',
            ], 400);
        }

        $optionIDs = [];

        foreach ($questionInfos as $info) {
            if (isset($info['questionID'], $info['optionID'])) {
                $optionIDs[$info['questionID']] = $info['optionID'];
            } else {
                return response()->json([
                    'message' => 'Invalid questionInfos structure.',
                ], 400);
            }
        }

        $data = [
            'finishedTime' => $request->finishedTime,
            'startedTime' => $request->startedTime,
            'questionInfos' => $questionInfos,
        ];

        $answerSheet = $this->StoringAnswerSheet($examId, $student, $optionIDs, $data);

        return response()->json([
            'message' => 'Answer sheet stored successfully.',
            'answerSheet' => $answerSheet
        ], 200);
    }


}
