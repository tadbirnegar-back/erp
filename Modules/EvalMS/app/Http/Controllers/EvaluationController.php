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
    public function index(Request $request): JsonResponse
    {
        $name = $request->name ?? null;
        $evals = Evaluation::when($name, function ($query) use ($name) {
            $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$name]);

        })->get();

//        $evals = Evaluation::all();
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

    public function detail(Request $request, $id): JsonResponse
    {
//        $eval = Evaluation::with('parts.indicators.parameters.parameterType','parts.indicators.parameters.options')->findOrFail($id);
//

        $perPage = $request->perPage ?? 10;
        $pageNum = $request->pageNum ?? 1;
        $user = \Auth::user();
//        $usersUnits = EvaluatorRepository::getOunits($user);
//        $usersUnits=$user->organizationUnits->pluck('descendantsAndSelf')->map(function ($organizationUnit, int $key) {
//            return $organizationUnit->pluck('id');
//        })->flatten()->unique();

        if (isset($request->filterUnit)) {
            $usersUnits = OrganizationUnit::with('descendantsAndSelf')->find($request->filterUnit)
                ->descendantsAndSelf;

        } else {
            $user->load('organizationUnits.descendantsAndSelf');
            $usersUnits = $user->organizationUnits
                ->pluck('descendantsAndSelf');
        }

        $usersUnits = $usersUnits
            ->flatten()
            ->values()
            ->pluck('id');

//        if (!is_null($usersUnits)) {
//            $ounitIDs = $usersUnits->pluck('organizationUnit.id')->reject(function ($head) {
//                return $head === null;
//            })->unique()->toArray();
//        } else {
//            $ounitIDs = [];
//        }

        $result = EvaluatorRepository::evalOfOunits($usersUnits->toArray(), $id, $perPage, $pageNum);
        $filters = $user->organizationUnits->map(function ($organizationUnit, int $key) {
            return $organizationUnit->descendantsAndSelf->toTree();
        });
        return response()->json(['result' => $result, 'filter' => $filters]);

    }

    public function ounitHistory(Request $request, $evalID, $ounitID)
    {
        $user = \Auth::user();
        $recordExists = $user->evaluators()
            ->where('evaluation_id', $evalID)
            ->where('organization_unit_id', $ounitID)
            ->exists();


        $ounit = OrganizationUnit::with(['ancestorsAndSelf.head.person'])->findOrFail($ounitID);

//        $usersUnits = EvaluatorRepository::getOunits($user, loadHeads: true);

//        $whoToFill = EvaluatorRepository::getOunitsParents($ounit);
        $whoToFill = $ounit->ancestorsAndSelf;

//        $headIDs = $usersUnits->pluck('organizationUnit.head.id')->reject(function ($head) {
//            return $head === null;
//        })->unique()->toArray();
//        $filteredModels = $whoToFill->filter(function ($model) use ($user) {
//            return $model->organizationunit?->head && $model->organizationunit?->head->id === $user->id;
//        });
//
//        $highestIndex = $filteredModels->keys()->max();
//        $filteredCollection = $whoToFill->filter(function ($model, $index) use ($highestIndex) {
//            return $index <= $highestIndex;
//        });

        $headIDs = $whoToFill->pluck('head.id')->reject(function ($head) {
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
