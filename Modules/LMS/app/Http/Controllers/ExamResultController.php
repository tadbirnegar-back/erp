<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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


    public function storeAnsS(Request $request, $examId)
    {
        $jsonData = $request->input('data');
        $data = json_decode($jsonData, true);
        $usedTime = $request->usedTime;
        if (!isset($data['questions'])) {
            return response()->json(['error' => 'Questions are missing.'], 400);
        }

        foreach ($data['questions'] as &$question) {
            if (!isset($question['option_id']) || $question['option_id'] === '') {
                $question['option_id'] = null;
            }
        }

        $student = Auth::user()->load('student');
        $optionID = array_filter(array_column($data['questions'], 'option_id'));

        $answerSheet = $this->storeAnswerSheet($examId, $student, $optionID, $data, $usedTime);

        $result = new ExamResultResource($answerSheet);
        return response()->json($result);
    }


}
