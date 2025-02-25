<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Traits\ReportingTrait;
use Modules\LMS\app\Models\Answers;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Option;
use Modules\LMS\app\Resources\CourseReportResource;
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
        if (!$courseID) {
            return response()->json(['message' => 'No Course found'], 403);
        }
        $answerSheetID = AnswerSheet::joinRelationship('exam.courseExams.course')
            ->orderBy('answer_sheets.start_date_time', 'desc')
            ->value('answer_sheets.id');


        $data = [
            'questions' => Answers::where('answer_sheet_id', $answerSheetID)
                ->get(['question_id', 'value as selected_option'])
                ->map(function ($answer) {
                    return [
                        'question_id' => $answer->question_id,
                        'option_id' => Option::where('title', $answer->selected_option)->where('question_id', $answer->question_id)->value('id')
                    ];
                })
                ->toArray()
        ];

        $result = $this->ans($answerSheetID, $student, $data, $courseID);
        return ReportingResource::make($result);

    }


    /**
     * Store a newly created resource in storage.
     */
    public function AllEnrollsCourseReport($courseID)
    {
        if (empty($courseID)) {
            return response()->json(['message' => 'No Course found'], 403);
        }

        $courseReport = $this->CourseInformation($courseID);
        return CourseReportResource::make($courseReport);
    }

}
