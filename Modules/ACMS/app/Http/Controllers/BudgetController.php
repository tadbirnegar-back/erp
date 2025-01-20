<?php

namespace Modules\ACMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Modules\ACMS\app\Http\Enums\AccountantScriptTypeEnum;
use Modules\ACMS\app\Http\Enums\SubjectTypeEnum;
use Modules\ACMS\app\Models\Budget;
use Modules\ACMS\app\Models\BudgetItem;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\ACMS\app\Resources\BudgetSingleResource;
use Modules\ACMS\app\Resources\VillageBudgetListResource;
use Modules\OUnitMS\app\Models\StateOfc;
use Morilog\Jalali\Jalalian;
use Validator;

class BudgetController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $recruitmentScripts = $user
            ->activeRecruitmentScripts()
            ->whereHas('scriptType', function ($query) {
                $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
            })
            ->with(['organizationUnit'])->get();

        $fiscalYears = FiscalYear::where('name', '>=', Jalalian::now()->getYear())->get()->pluck('id');

        $ounits = $recruitmentScripts->pluck('organizationUnit.id');

        $budgets = Budget::joinRelationship('statuses', [
            'statuses' => function ($join) {
                $join
                    ->whereRaw('bgtBudget_status.create_date = (SELECT MAX(create_date) FROM bgtBudget_status WHERE budget_id = bgt_budgets.id)');
            }
        ])
            ->joinRelationship('ounitFiscalYear.village')
            ->whereIntegerInRaw('ounit_fiscalYear.ounit_id', $ounits->toArray())
            ->whereIntegerInRaw('fiscal_year_id', $fiscalYears->toArray())
            ->select([
                'organization_units.name as ounit_name',
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'bgt_budgets.id as budget_id',
                'bgt_budgets.name as budget_name',
                'village_ofcs.abadi_code as village_abadicode',
            ])
            ->get();
        return VillageBudgetListResource::collection($budgets);


    }

    public function villagesBudgetsByOunitID(Request $request)
    {
        $data = $request->all();
        $validation = Validator::make($data, [
            'ounitID' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 422);
        }

        $budgets = Budget::joinRelationship('statuses', [
            'statuses' => function ($join) {
                $join->on('bgtBudget_status.id', '=', \DB::raw('(
                                SELECT id
                                FROM bgtBudget_status AS ps
                                WHERE ps.budget_id = bgt_budgets.id
                                ORDER BY ps.create_date DESC
                                LIMIT 1
                            )'));
            }
        ])
            ->joinRelationship('ounitFiscalYear.village')
            ->where('ounit_fiscalYear.ounit_id', $data['ounitID'])
            ->select([
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'bgt_budgets.id as budget_id',
                'bgt_budgets.name as budget_name',
                'village_ofcs.abadi_code as village_abadicode',
            ])
            ->get();

        return VillageBudgetListResource::collection($budgets);

    }

    public function show($id)
    {
        $budget = Budget::with([
            'fiscalYear',
            'circularFile',
            'ounit.ancestors' => function ($q) {
                $q->where('unitable_type', '!=', StateOfc::class);
            },
            'statuses.pivot.person',
            'village',
            'statuses',
            'latestStatus'
        ])
            ->find($id);

        if (is_null($budget)) {
            return response()->json(['error' => 'بودجه مورئ نظر یافت نشد'], 404);
        }

        return BudgetSingleResource::make($budget);
    }

    public function budgetSubjects(Request $request)
    {
        $data = $request->all();
        $validation = Validator::make($data, [
            'budgetID' => 'required',
            'subjectTypeID' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 422);
        }
        $select = [
            'bgt_budget_items.id as item_id',
            'bgt_budget_items.finalized_amount as approved_amount',
            'bgt_budget_items.proposed_amount as proposed_amount',
            'bgt_circular_subjects.id as id', 'bgt_circular_subjects.name as name',
            'bgt_circular_subjects.id as id', 'bgt_circular_subjects.parent_id as parent_id',
        ];

        if ($request->subjectTypeID == SubjectTypeEnum::INCOME->value) {
            $select[] = 'bgt_budget_items.percentage as percentage';
        }

        $budget = BudgetItem::joinRelationship('circularItem.subject')
            ->where('budget_id', $request->budgetID)
            ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::from($request->subjectTypeID)->value)
            ->select($select)
            ->get();

        return response()->json(['data' => $budget->toHierarchy()], 200);
    }
}
