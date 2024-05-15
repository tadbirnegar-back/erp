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
    public function store(Request $request, $evalID, $ounitID): JsonResponse
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
            $data['organizationUnitID'] = $ounitID;
            $evaluator = EvaluatorRepository::evaluatorStore($data, $evalID, $data['userID']);
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
    public function hasEvaluationRecord()
    {


        $user = \Auth::user();

        $assignedEvals = $user->load(['organizationUnits.evaluations.evaluators'=>function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }]);

        if (is_null($user->organizationUnits) || is_null($user->organizationUnits->pluck('evaluations'))) {
            $recordExists = true;
        }else{
            $hasAtLeastOneEvaluation = false;
            $allEmpty = true;

            foreach ($assignedEvals->organizationUnits as $organizationUnit) {
                if (! $organizationUnit->evaluations->isEmpty()) {
                    $hasAtLeastOneEvaluation = true;
                    break; // Exit loop if at least one evaluation is found
                }
                $allEmpty = $allEmpty && $organizationUnit->evaluations->isEmpty();
            }
            if ($hasAtLeastOneEvaluation) {
                $filledEvaluations = [];
                $unfilledEvaluations = [];

                foreach ($assignedEvals->organizationUnits as $organizationUnit) {
                    foreach ($organizationUnit->evaluations as $evaluation) {
                        // Check if user has filled the evaluation (replace 'evaluator_id' with your actual column name)
                        foreach ($evaluation->evaluators as $evaluator) {
                            if ($evaluator->user_id === $user->id ) {
                                $filledEvaluations[] = $evaluation;
                            } else {
                                $unfilledEvaluations[] = $evaluation;
                            }
                        }


                    }
                }

                // Handle unfilled evaluations (optional)
                if (!empty($unfilledEvaluations)) {
                    // Access details of unfilled evaluations here (e.g., loop and print names)
                    $recordExists = true;
                }else{
                    $recordExists = false;
                }

            }else{
                $recordExists = true;
            }
    }
        return response()->json([
            'hasRecord' => $recordExists,
        ]);
    }
}
