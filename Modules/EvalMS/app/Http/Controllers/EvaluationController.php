<?php

namespace Modules\EvalMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\EvalMS\app\Http\Repositories\EvaluatorRepository;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\OUnitMS\app\Models\OrganizationUnit;

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
    public function show(Request $request, $evalID, $ounitID): JsonResponse
    {
//        $eval = Evaluation::with('parts.indicators.parameters.parameterType', 'parts.indicators.parameters.options')->findOrFail($id);
//

//        $user = \Auth::user();
//        $a = EvaluatorRepository::getOunitsWithSubsOfUser($user);
//        $ounitIDs = $a->pluck('organizationUnit.id');
//        $result = EvaluatorRepository::evalOfOunits($ounitIDs->toArray(), $id);
//
        $ounit = OrganizationUnit::findOrFail($ounitID);

//        $usersUnits = EvaluatorRepository::getOunits($user, loadHeads: true);

        $whoToFill = EvaluatorRepository::getOunitsParents($ounit);

//        $headIDs = $usersUnits->pluck('organizationUnit.head.id')->reject(function ($head) {
//            return $head === null;
//        })->unique()->toArray();

        $headIDs = $whoToFill->pluck('organizationUnit.head.id')->reject(function ($head) {
            return $head === null;
        })->unique()->toArray();
//        $unitIDs = $usersUnits->pluck('organizationUnit.id')->reject(function ($head) {
//            return $head === null;
//        })->unique()->toArray();

//        $pageNum = $request->pageNum ?? 1;
//        $perPage = $request->perPage ?? 10;

        $result = EvaluatorRepository::getEvalOunitHistory($evalID, $ounitID, $headIDs);
        $result['relatedUnits'] = $whoToFill;
        return response()->json($result);
//        return response()->json($eval);

    }

    public function detail($id): JsonResponse
    {
//        $eval = Evaluation::with('parts.indicators.parameters.parameterType','parts.indicators.parameters.options')->findOrFail($id);
//

        $user = \Auth::user();
        $usersUnits = EvaluatorRepository::getOunits($user);
        if (!is_null($usersUnits)) {
            $ounitIDs = $usersUnits->pluck('organizationUnit.id')->reject(function ($head) {
                return $head === null;
            })->unique()->toArray();
        } else {
            $ounitIDs = [];
        }

        $result = EvaluatorRepository::evalOfOunits($ounitIDs, $id);

        return response()->json($result);

    }

    public function ounitHistory(Request $request, $evalID, $ounitID)
    {
        $user = \Auth::user();
        $recordExists = $user->evaluators()
            ->where('evaluation_id', $evalID)
            ->where('organization_unit_id',$ounitID)
            ->exists();



        $ounit = OrganizationUnit::findOrFail($ounitID);

//        $usersUnits = EvaluatorRepository::getOunits($user, loadHeads: true);

        $whoToFill = EvaluatorRepository::getOunitsParents($ounit);

//        $headIDs = $usersUnits->pluck('organizationUnit.head.id')->reject(function ($head) {
//            return $head === null;
//        })->unique()->toArray();

        $headIDs = $whoToFill->pluck('organizationUnit.head.id')->reject(function ($head) {
            return $head === null;
        })->unique()->toArray();
//        $unitIDs = $usersUnits->pluck('organizationUnit.id')->reject(function ($head) {
//            return $head === null;
//        })->unique()->toArray();

        $pageNum = $request->pageNum ?? 1;
        $perPage = $request->perPage ?? 10;

        $result = EvaluatorRepository::getEvalOunitHistory($evalID, $ounitID, $headIDs, $pageNum, $perPage);
        $result['relatedUnits'] = $whoToFill;
        $result['hasRecord'] = $recordExists;
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
