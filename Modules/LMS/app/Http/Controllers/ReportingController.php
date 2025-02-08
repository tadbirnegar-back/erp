<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\LMS\app\Http\Traits\ReportingTrait;
use Modules\LMS\app\Models\Answers;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Option;
use Modules\LMS\app\Resources\ReportingResource;

class ReportingController extends Controller
{
    use ReportingTrait;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index($courseID)
    {
        $student = Auth::user()->load('student');
        $answerSheetID = AnswerSheet::joinRelationship('exam.courseExams.course')
            ->orderBy('answer_sheets.start_date_time', 'desc')
            ->value('answer_sheets.id');


        if (!$answerSheetID) {
            return response()->json(['message' => 'No Answer Sheet found'], 404);
        }

        $data = [
            'questions' => Answers::where('answer_sheet_id', $answerSheetID)
                ->get(['question_id', 'value as selected_option'])
                ->map(function ($answer) {
                    return [
                        'question_id' => $answer->question_id,
                        'option_id' => Option::where('title', $answer->selected_option)->value('id')
                    ];
                })->toArray()
        ];

        $result = $this->ans($answerSheetID, $student, $data, $courseID);
        return ReportingResource::make($result);

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }
}
