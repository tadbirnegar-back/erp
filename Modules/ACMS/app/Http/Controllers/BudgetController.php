<?php

namespace Modules\ACMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Modules\ACMS\app\Http\Enums\AccountantScriptTypeEnum;
use Modules\ACMS\app\Models\Budget;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\ACMS\app\Resources\VillageBudgetListResource;
use Morilog\Jalali\Jalalian;

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

}
