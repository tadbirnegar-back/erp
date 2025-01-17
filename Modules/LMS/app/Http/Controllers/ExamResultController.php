<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Traits\AnswerSheetTrait;
use Modules\LMS\app\Http\Traits\ExamResultTrait;
use Modules\LMS\app\Models\Answers;
use Modules\LMS\app\Models\Option;
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

        $student = User::with('student')->find(68);
        $optionID = array_filter(array_column($data['questions'], 'option_id'));

        $answerSheet = $this->storeAnswerSheet($examId, $student, $optionID, $data, $usedTime);
        return response()->json(['answer_sheet_id' => $answerSheet['answerSheet']->id], 200);
    }

    public function showAns($answerSheetId)
    {
        $student = Auth::user()->load('student');
        $data = [
            'questions' => Answers::where('answer_sheet_id', $answerSheetId)
                ->get(['question_id', 'value as selected_option'])
                ->map(function ($answer) {
                    return [
                        'question_id' => $answer->question_id,
                        'option_id' => Option::where('title', $answer->selected_option)->value('id')
                    ];
                })
                ->toArray()
        ];

        $result = $this->Show($answerSheetId, $student, $data);
        $response = new ExamResultResource(collect($result));
        return $response;
    }


}
