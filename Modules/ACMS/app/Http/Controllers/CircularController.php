<?php

namespace Modules\ACMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\ACMS\app\Http\Trait\BudgetItemsTrait;
use Modules\ACMS\app\Http\Trait\BudgetTrait;
use Modules\ACMS\app\Http\Trait\CircularTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\ACMS\app\Http\Trait\OunitFiscalYearTrait;
use Modules\ACMS\app\Models\Circular;
use Modules\ACMS\app\Models\CircularSubject;
use Modules\ACMS\app\Resources\CircularListResource;
use Modules\ACMS\app\Resources\CircularShowResource;
use Validator;

class CircularController extends Controller
{
    use FiscalYearTrait, CircularTrait, OunitFiscalYearTrait, BudgetTrait, BudgetItemsTrait;

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $user = Auth::user();
        $validate = Validator::make($data, [
            'fiscalYearName' => 'required',
            'circularName' => 'required',
            'fileID' => 'required',
            'startDate' => 'required',
            'finishDate' => 'required',
        ]);


        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }

        $data['userID'] = $user->id;

        try {
            DB::beginTransaction();
            $fiscalYear = $this->createFiscalYear($data);
            $circular = $this->createCircular($data, $fiscalYear);
            $circularSubjects = CircularSubject::get(['id']);
            $circular->circularSubjects()->sync($circularSubjects->pluck('id')->toArray());

            DB::commit();
            return response()->json(['data' => $circular]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function edit($id)
    {
        $circular = Circular::with('fiscalYear:id,name', 'file:id,slug,name,size')->find($id);

        if (is_null($circular)) {
            return response()->json(['message' => 'بخشنامه مورد نظر یافت نشد'], 404);
        }

        return CircularShowResource::make($circular);
    }

    public function update(Request $request, $id)
    {

        $data = $request->all();
        $data['circularID'] = $id;
        $validate = Validator::make($data, [
            'fileID' => 'required',
            'circularID' => 'exists:bgt_circulars,id',
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $circular = Circular::find($data['circularID']);
            $circular->file_id = $data['fileID'];
            $circular->save();

            DB::commit();
            return response()->json(['message' => 'با موفقیت بروز شد'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $circular = Circular::with('latestStatus')->find($id);

            if (is_null($circular)) {
                return response()->json(['message' => 'بخشنامه مورد نظر یافت نشد'], 404);
            }
            $deleteStatus = $this->deleteCircularStatus();
            if ($circular->latestStatus->id == $deleteStatus->id) {
                return response()->json(['message' => ' بخشنامه از قبل حذف شده است'], 403);
            }

            $data = [
                'statusID' => $deleteStatus->id,
                'userID' => $user->id,
            ];
            $circularStatus = $this->circularStatusAttach($data, $circular);

            DB::commit();
            return response()->json(['message' => 'بخشنامه حذف شد'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function index(Request $request)
    {
        $data = $request->all();

        $circulars = $this->indexCircular($data);

        return CircularListResource::collection($circulars);

    }

    public function show($id)
    {
        $circular = Circular::with(['circularSubjects' => function ($query) {
            $query->withoutGlobalScopes();
        }, 'latestStatus:name,class_name', 'file:id,slug,name,size'])->find($id);

        if (is_null($circular)) {
            return response()->json(['message' => 'بخشنامه مورد نظر یافت نشد'], 404);
        }

        return CircularShowResource::make($circular);

    }

    public function unitsIncludingForAddingBudgetCount(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'circularID' => ['required', 'exists:bgt_circulars,id'],
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }

        $circular = Circular::find($data['circularID']);
        $includedOunitsForBudgetCount = $this->ounitsIncludingForAddingBudget($circular->fiscal_year_id, true);

        return response()->json(['data' => ['count' => $includedOunitsForBudgetCount]], 200);
    }

    public function dispatchCircularToVillages(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $validate = Validator::make($data, [
            'circularID' => ['required'],
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }

        $circular = Circular::with('fiscalYear', 'circularItems')->find($data['circularID']);
        if (is_null($circular)) {
            return response()->json(['message' => 'بخشنامه مورد نظر یافت نشد'], 404);
        }

        try {
            DB::beginTransaction();
            $includedOunitsForBudget = $this->ounitsIncludingForAddingBudget($circular->fiscalYear->id, false)
                ->chunk(500);
            $fiscalYear = $circular->fiscalYear;

            $includedOunitsForBudget->each(function ($chunkedUnits, $key) use ($fiscalYear, $user, $circular) {

                $chunkedUnits = $chunkedUnits->values();

                $ounitFiscalYears = $this->bulkStoreOunitFiscalYear($chunkedUnits->toArray(), $fiscalYear, $user);

                $budgetName = 'بودجه سال ' . $fiscalYear->name;
                $budgets = $this->bulkStoreBudget($ounitFiscalYears->toArray(), $budgetName, $user);

                $budgets->each(function ($budget) use ($circular, $user) {
                    $budgetItems = $this->bulkStoreBudgetItems($budget, $circular->circularItems->toArray());
                });
            });
            $this->circularStatusAttach([
                'userID' => $user->id,
                'statusID' => $this->approvedCircularStatus()->id,

            ], $circular);
            DB::commit();
            return response()->json(['message' => 'با موفقیت بروز شد'], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
