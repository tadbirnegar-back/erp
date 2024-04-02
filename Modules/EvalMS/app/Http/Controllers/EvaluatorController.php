<?php

namespace Modules\EvalMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\EvalMS\app\Http\Repositories\EvaluatorRepository;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\EvalMS\app\Models\Evaluator;

class EvaluatorController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,$id): JsonResponse
    {
        $data = $request->all();
        $data['userID'] = \Auth::user()->id;

//        $hasEvaluated = Evaluator::where('user_id', $data['userID'])
//            ->where('evaluation_id', $id)
//            ->exists();
//        if ($hasEvaluated) {
//            return response()->json(['message' => 'شما قبلا در این نظر سنجی شرکت کرده اید'],400);
//
//        }
        try {
            \DB::beginTransaction();
            $evaluator = EvaluatorRepository::evaluatorStore($data, $id, $data['userID']);
//            return response()->json(['message' => $evaluator]);

            $answers = json_decode($data['answers'], true);
            $evalAnswers = EvaluatorRepository::answerStore($answers, $evaluator->id);

            \DB::commit();
            return response()->json(['message' => 'باموفقیت ثبت شد']);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در ثبت پاسخ'], 500);

        }

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
