<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LMS\app\Http\Traits\questionsTrait;

class QuestionsController extends Controller
{
    use questionsTrait;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function getDropDowns($courseID)
    {

        $data = $this->dropDowns($courseID);
        return response()->json($data);
    }

    public function storeQuestion(Request $request, $courseID)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'questionTypeID' => 'required|integer|exists:question_types,id',
            'repositoryID' => 'required|integer|exists:repositories,id',
            'lessonID' => 'required|integer|exists:lessons,id',
            'difficultyID' => 'required|integer|exists:difficulties,id',
            'options' => 'required|string',

        ]);

        $options = json_decode($validated['options'], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['message' => 'Invalid options format'], 400);
        }

        $question = $this->insertQuestionWithOptions($validated, $options, $courseID);

        if ($question) {
            return response()->json([
                'message' => 'Question created successfully',
                'question' => $question,
            ], 201);
        }

        return response()->json(['message' => 'Failed to create question'], 500);
    }

    public function showDropDowns($courseID)
    {

        $show = $this->dropDowns($courseID);
        if (!$show) {
            return response()->json([
                'error' => 'Course not found.'
            ], 404);
        }
        return response()->json($show);
    }


}
