<?php

namespace Modules\EvalMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\EvalMS\app\Http\Repositories\EvaluatorRepository;
use Modules\EvalMS\app\Models\Evaluation;

class EvaluationController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $evals = Evaluation::all();

        return response()->json($evals);
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
//        $eval = Evaluation::with('parts.indicators.parameters.parameterType','parts.indicators.parameters.options')->findOrFail($id);
//

        $user = \Auth::user();
        $a = EvaluatorRepository::getOunitsWithSubsOfUser($user);
        $ounitIDs = $a->pluck('organizationUnit.id');
        $result = EvaluatorRepository::evalOfOunits($ounitIDs->toArray(), $id);

        return response()->json($result);

    }

    public function ounitHistory(Request $request, $evalID, $ounitID)
    {
        $user = \Auth::user();

        $usersUnits = EvaluatorRepository::getOunitsWithSubsOfUser($user, loadHeads: true);
        $headIDs = $usersUnits->pluck('organizationUnit.head.id');

        $pageNum = $request->pageNume ?? 1;
        $perPage = $request->perPage ?? 10;

        $result = EvaluatorRepository::getEvalOunitHistory($evalID, $ounitID, $headIDs, $pageNum, $perPage);

        return response()->json($result);

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
