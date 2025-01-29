<?php

namespace Modules\ACMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Bus\Batch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Mockery\Exception;
use Modules\ACMS\app\Http\Enums\BudgetStatusEnum;
use Modules\ACMS\app\Http\Enums\SubjectTypeEnum;
use Modules\ACMS\app\Http\Trait\BudgetItemsTrait;
use Modules\ACMS\app\Http\Trait\BudgetTrait;
use Modules\ACMS\app\Http\Trait\CircularTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\ACMS\app\Http\Trait\OunitFiscalYearTrait;
use Modules\ACMS\app\Jobs\DispatchCircularForOunitJob;
use Modules\ACMS\app\Models\Budget;
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
        $circular = Circular::
        joinRelationship('file')
            ->joinRelationship('fiscalYear')
            ->addSelect([
                'files.id as file_id',
                'files.name as file_name',
                'files.slug as file_slug',
                'files.size as file_size',
                'fiscal_years.id as fiscal_year_id',
                'fiscal_years.name as fiscal_year_name',
            ])
            ->find($id);


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
        /**
         * @var Circular $circular
         */
        $circular = Circular::joinRelationship('statuses', ['statuses' => function ($join) {
            $join
                ->whereRaw('bgtCircular_status.create_date = (SELECT MAX(create_date) FROM bgtCircular_status WHERE circular_id = bgt_circulars.id)');
        }
        ])
            ->joinRelationship('file')
            ->joinRelationship('fiscalYear')
            ->addSelect([
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'files.id as file_id',
                'files.name as file_name',
                'files.slug as file_slug',
                'files.size as file_size',
                'fiscal_years.id as fiscal_year_id',
                'fiscal_years.name as fiscal_year_name',
            ])
            ->find($id);


        if (is_null($circular)) {
            return response()->json(['message' => 'بخشنامه مورد نظر یافت نشد'], 404);
        }

        $budgetsCount = Budget::joinRelationship('statuses', [
            'statuses' => function ($join) {
                $join
                    ->whereRaw('bgtBudget_status.create_date = (SELECT MAX(create_date) FROM bgtBudget_status WHERE budget_id = bgt_budgets.id)');
            }
        ])
            ->where('bgt_budgets.circular_id', $id)
            ->where('statuses.name', '!=', BudgetStatusEnum::CANCELED->value)
            ->select([
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                DB::raw('COUNT(*) as count'),
            ])
            ->groupBy(['statuses.name', 'statuses.class_name'])
            ->get();

        $subjectsCount = Circular::joinRelationship('circularSubjects')
            ->select([
                'bgt_circular_subjects.subject_type_id',
                DB::raw('COUNT(*) as count'),

            ])
            ->groupBy('bgt_circular_subjects.subject_type_id')
            ->where('bgt_circulars.id', $id)
            ->get();

        DB::enableQueryLog();
        $dispatchedOunits = $this->ounitsIncludingForAddingBudget($circular, true, true);
        $unDispatchedOunits = $this->ounitsIncludingForAddingBudget($circular, true, false);
        $queries = DB::getQueryLog();
//        return response()->json($queries);

        $circular->setAttribute('dispatchedOunits', $dispatchedOunits);
        $circular->setAttribute('unDispatchedOunits', $unDispatchedOunits);
        $circular->setAttribute('budgetCounts', $budgetsCount);
        $circular->setAttribute('subjects', $subjectsCount);

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
        $includedOunitsForBudgetCount = $this->ounitsIncludingForAddingBudget($circular, true);

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
            \DB::beginTransaction();

            $includedOunitsForBudget = $this->ounitsIncludingForAddingBudget($circular, false)
                ->chunk(150);

            $jobs = [];
            $includedOunitsForBudget->each(function ($chunkedUnits, $key) use ($user, $circular, &$jobs) {
                $chunkedUnits = $chunkedUnits->values();

                $jobs[] = new DispatchCircularForOunitJob($chunkedUnits->toArray(), $circular, $user);
            });

            Bus::batch($jobs)
                ->then(function (Batch $batch) {
                    // All jobs completed successfully
                    \Log::info("All jobs in the batch have completed successfully.");
                })
                ->catch(function (Batch $batch, \Throwable $e) {
                    // Handle the exception
                    \Log::error("An error occurred in the batch: " . $e->getMessage());
                })
                ->finally(function (Batch $batch) {
                    // This block runs regardless of success or failure
                    \Log::info("Batch processing is complete.");
                })
                ->name('DispatchCircularForOunitJob')
                ->onQueue('default')
                ->dispatchAfterResponse();
            $this->circularStatusAttach([
                'userID' => $user->id,
                'statusID' => $this->approvedCircularStatus()->id,

            ], $circular);
            \DB::commit();
            return response()->json(['message' => 'با موفقیت بروز شد'], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function subjectsOfCircular(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'circularID' => ['required', 'exists:bgt_circulars,id'],
            'subjectTypeID' => ['required'],
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }
        $subjectType = SubjectTypeEnum::tryFrom($request->subjectTypeID);

        if (is_null($subjectType)) {
            return response()->json(['message' => 'نوع موضوع مورد نظر یافت نشد'], 422);
        }

        $subjects = CircularSubject::withoutGlobalScopes()
            ->joinRelationship('circulars')
            ->where('bgt_circulars.id', $request->circularID)
            ->where('bgt_circular_subjects.subject_type_id', $subjectType->value)
            ->select([
                'bgt_circular_items.percentage as percentage',
                'bgt_circular_items.id as item_id',
                'bgt_circular_subjects.code as code',
                'bgt_circular_subjects.id as id',
                'bgt_circular_subjects.parent_id as parent_id',
                'bgt_circular_subjects.name as name',
                'bgt_circular_subjects.subject_type_id as subject_type_id',

            ])
            ->get();


        return response()->json(['data' => $subjects->toHierarchy()], 200);

    }

}
