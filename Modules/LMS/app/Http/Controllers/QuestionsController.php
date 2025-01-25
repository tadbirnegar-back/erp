<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Http\Traits\questionsTrait;
use Modules\LMS\app\Models\Question;
use Modules\LMS\app\Resources\EditedQuestionResource;
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
        try {
            DB::beginTransaction();

            $data = $request->all();
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'questionTypeID' => 'required|integer|exists:question_types,id',
                'repositoryID' => 'required|string',
                'lessonID' => 'required|integer|exists:lessons,id',
                'difficultyID' => 'required|integer|exists:difficulties,id',
                'options' => 'required|string',
            ]);


            $options = json_decode($validated['options'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'Invalid options format'], 400);
            }
            $repositoryIDs = json_decode($validated['repositoryID'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'Invalid repositoryID format'], 400);
            }

            $repositoryIDs = $repositoryIDs['ids'];


            $user = Auth::user();

            $question = $this->insertQuestionWithOptions($data, $options, $courseID, $user, $repositoryIDs);
            DB::commit();
            if ($question) {
                return response()->json([
                    'message' => 'Question created successfully',
                ], 201);
            }

            return response()->json(['message' => 'Failed to create question'], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred while processing your request.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function showDropDowns($courseID)
    {
        try {
            $show = $this->dropDowns($courseID);
            if (!$show) {
                return response()->json([
                    'error' => 'Course not found.'
                ], 403);
            }
            return new QuestionResource(collect($show));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching dropdowns.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function questionsManagement($id)
    {
        try {
            if (!$id) {
                return response()->json([
                    'error' => 'CourseID not found.'
                ], 404);
            }
            $question = $this->questionList($id);
            return new QuestionManagementResource(collect($question));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching questions.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteQuestionAndRelatedOptions($questionID)
    {
        try {
            DB::beginTransaction();

            $question = $this->questionDelete($questionID);
            DB::commit();

            return response()->json(['message' => 'Question status updated to inactive successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred while updating the question status.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function showQuestion($questionID)
    {
        $response = $this->showEditedQuestion($questionID);
        return new EditedQuestionResource(collect($response));
    }

    public function update(Request $request, $questionID)
    {
        try {
            $data = $request->all();

            $options = json_decode($data['options'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'Invalid options format. Please provide a valid JSON string.'
                ], 400);
            }
            $repositoryIDs = json_decode($data['repositoryID'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'Invalid repositoryID format'], 400);
            }

            $repositoryIDs = $repositoryIDs['ids'];

            $delete = json_decode($data['delete'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'Invalid delete format. Please provide a valid JSON string.'
                ], 400);
            }

            $question = Question::find($questionID);
            if (!$question) {
                return response()->json([
                    'message' => 'Question not found'
                ], 404);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $updateResult = $this->updateQuestionWithOptions($questionID, $data, $options, $user, $delete, $repositoryIDs);
            if ($updateResult) {
                return response()->json([
                    'message' => 'Question updated successfully'
                ], 200);
            }

            return response()->json([
                'message' => 'Failed to update question'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the question.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
