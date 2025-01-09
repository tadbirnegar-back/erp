<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
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


    public function storeAnsS($id)
    {
        $auth = User::with('student')->find(68);


        $validated = validator(request()->all(), [
            'optionID' => 'required',
            'questionID' => 'required'
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 400);
        }

        $student = $auth->student;

        $result = $this->StoringAnswerSheet($id, $student, $auth);

        return response()->json($result);
    }


}
