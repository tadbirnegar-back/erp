<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Traits\questionsTrait;
use Modules\LMS\app\Models\Question;
use Modules\LMS\app\Resources\QuestionManagementResource;
use Modules\LMS\app\Resources\QuestionResource;

class QuestionsController extends Controller
{
    use questionsTrait;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function storeQuestionAndOptions(Request $request, $courseID)
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
        $user = User::find(2203);

        $question = $this->insertQuestionWithOptions($validated, $options, $courseID, $user);

        if ($question) {
            return response()->json([
                'message' => 'Question created successfully',
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
        return new QuestionResource(collect($show));
    }

    public function questionsManagement($id)
    {
        if (!$id) {
            return response()->json([
                'courseID not found.'
            ], 404);
        }
        $question = $this->questionList($id);
        return new QuestionManagementResource(collect($question));
    }

    public function deleteQuestionAndRelatedOptions($questionID)
    {
        $question = Question::findOrFail($questionID);

        $question->options()->delete();

        $question->delete();

        return response()->json(['message' => 'Question deleted successfully.']);
    }


}
