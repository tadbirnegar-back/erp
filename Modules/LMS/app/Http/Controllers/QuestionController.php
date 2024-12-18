<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Traits\QuestionsTrait;
use Modules\LMS\app\Models\Question;

class QuestionController extends Controller
{
    use QuestionsTrait;

    public function store(Request $request)
    {
        //   $user=auth()->user();
        $user = User::find(40);

        $data = $request->all();
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
//            'creatorID' => 'required|integer|exists:users,id',
            'difficultyID' => 'required|integer|exists:difficulties,id',
            'lessonID' => 'required|integer|exists:lessons,id',
            'questionTypeID' => 'required|integer|exists:question_types,id',
            'repositoryID' => 'nullable|integer|exists:repositories,id',
            'createDate' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $question = $this->insertOptions([$data], $user);
//            return response()->json($question);

            DB::commit();

            return response()->json(['message' => 'Success', 'data' => $question], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در افزودن سوال', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroyQuestion($id, Request $request)
    {
        $success = $this->deleteQuestionRecord($id);

        if ($success) {
            return response()->json(['message' => 'Question has been deactivated successfully.'], 200);
        } else {
            return response()->json(['message' => 'Question not found.'], 404);
        }


    }

    public function editQuestion(array $data, Question $question)
    {
        $question = Question::findOrFail($data['id']);
        if ($question == null) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        try {
            DB::beginTransaction();
            $question = $this->UpdateQuestion($data, $question);
            DB::commit();
            return response()->json(['message' => 'Success', 'data' => $question], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش سوال', 'error' => $e->getMessage()], 500);
        }
    }


}
