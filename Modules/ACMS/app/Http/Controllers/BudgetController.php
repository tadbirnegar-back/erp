<?php

namespace Modules\ACMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\ACC\app\Http\Enums\DocumentStatusEnum;
use Modules\ACC\app\Http\Enums\DocumentTypeEnum;
use Modules\ACMS\app\Http\Enums\AccountantScriptTypeEnum;
use Modules\ACMS\app\Http\Enums\BudgetStatusEnum;
use Modules\ACMS\app\Http\Enums\SubjectTypeEnum;
use Modules\ACMS\app\Http\Trait\BudgetTrait;
use Modules\ACMS\app\Models\Budget;
use Modules\ACMS\app\Models\BudgetItem;
use Modules\ACMS\app\Models\BudgetStatus;
use Modules\ACMS\app\Models\CircularSubject;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\ACMS\app\Resources\BudgetSingleResource;
use Modules\ACMS\app\Resources\TafriqBudgetExpanse;
use Modules\ACMS\app\Resources\TafriqBudgetIncome;
use Modules\ACMS\app\Resources\VillageBudgetListResource;
use Modules\HRMS\app\Models\ScriptType;
use Modules\OUnitMS\app\Models\StateOfc;
use Morilog\Jalali\Jalalian;
use Validator;

class BudgetController extends Controller
{
    use BudgetTrait;

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

        $budgets = Budget::
        whereNotIn('bgt_budgets.id', function ($query) {
            $query->select('bgt_budgets.parent_id')
                ->from('bgt_budgets')
                ->whereNotNull('bgt_budgets.parent_id');
        })
            ->joinRelationship('statuses', [
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
                'bgt_budgets.ounitFiscalYear_id',
                'bgt_budgets.name as budget_name',
                'village_ofcs.abadi_code as village_abadicode',
                'bgt_budgets.isSupplementary as isSupplementary'
            ])
            ->with(['fiscalYear'])
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

        $budgets = Budget::whereNotIn('bgt_budgets.id', function ($query) {
            $query->select('bgt_budgets.parent_id')
                ->from('bgt_budgets')
                ->whereNotNull('bgt_budgets.parent_id');
        })
            ->joinRelationship('statuses', [
            'statuses' => function ($join) {
                $join->on('bgtBudget_status.id', '=', DB::raw('(
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
        $budget = Budget::
        with([
            'fiscalYear',
            'circularFile',
            'ounit.ancestors' => function ($q) {
                $q->where('unitable_type', '!=', StateOfc::class);
            },
            'statuses.pivot.person',
            'statuses.pivot.file',
            'village',
            'statuses',
            'latestStatus',
            'ancestors.statuses' => function ($q) {
                $q->with(['pivot.person', 'pivot.file']);
            },
            'ancestors.latestStatus',
            'ounitHead',
            'financialManager',
//            'ounit.person',
        ])
            ->find($id);

        if (is_null($budget)) {
            return response()->json(['error' => 'بودجه مورد نظر یافت نشد'], 404);
        }
        $scriptType = ScriptType::where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value)->first();

//        $financialManager = RecruitmentScript::join('recruitment_script_status as rss', 'recruitment_scripts.id', '=', 'rss.recruitment_script_id')
//            ->join('statuses as s', 'rss.status_id', '=', 's.id')
//            ->where('s.name', 'فعال')
//            ->where('rss.create_date', function ($subQuery) {
//
//                $subQuery->selectRaw('MAX(create_date)')
//                    ->from('recruitment_script_status as sub_rss')
//                    ->whereColumn('sub_rss.recruitment_script_id', 'rss.recruitment_script_id');
//            })
//            ->where('script_type_id', $scriptType->id)
//            ->where('organization_unit_id', $budget->ounit->id)
//            ->with(['user.person'])
//            ->first();

        $incomeResults = BudgetItem::joinRelationship('circularItem.subject')
            ->where('budget_id', $id
            )
            ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::INCOME->value)
            ->select([
                DB::raw('SUM(bgt_budget_items.proposed_amount * COALESCE(bgt_budget_items.percentage, 0) / 100) AS jari_income_total'),
                DB::raw('SUM(bgt_budget_items.proposed_amount) - SUM(bgt_budget_items.proposed_amount * COALESCE(bgt_budget_items.percentage, 0) / 100) AS operational_income_total'),
                DB::raw('COUNT(*) as count'),

            ])
            ->first();

        $economicResults = BudgetItem::joinRelationship('circularItem.subject')
            ->where('budget_id', $id
            )
            ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::ECONOMIC_EXPENSE->value)
            ->select([
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(bgt_budget_items.proposed_amount) AS economic_total'),
            ])
            ->first();
        $operationalResults = BudgetItem::joinRelationship('circularItem.subject')
            ->where('budget_id', $id)
            ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::OPERATIONAL_EXPENSE->value)
            ->select([
                DB::raw('SUM(bgt_budget_items.proposed_amount) AS operational_total'),
                DB::raw('COUNT(*) as count'),

            ])
            ->first();

        $budget->setAttribute('income_sum', $incomeResults);
        $budget->setAttribute('eco_sum', $economicResults);
        $budget->setAttribute('operational_sum', $operationalResults);


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

    public function changeBudgetStatus(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'budgetID' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $budget = Budget::
            with([
                'latestStatus',
            ])
                ->find($request->budgetID);

            if (is_null($budget)) {
                return response()->json(['error' => 'بودجه مورد نظر یافت نشد'], 404);
            }

            $status = $budget->latestStatus->name;

            $nextStatus = match ($status) {
                BudgetStatusEnum::PROPOSED->value => BudgetStatusEnum::PENDING_FOR_APPROVAL->value,
                BudgetStatusEnum::PENDING_FOR_APPROVAL->value => BudgetStatusEnum::PENDING_FOR_HEYAAT_APPROVAL->value,
                BudgetStatusEnum::PENDING_FOR_HEYAAT_APPROVAL->value => BudgetStatusEnum::FINALIZED->value,
                default => null,
            };

            if (is_null($nextStatus)) {
                return response()->json(['error' => 'امکان تغییر وضعیت بودجه وجود ندارد'], 400);
            }
            $status = Budget::GetAllStatuses()->where('name', $nextStatus)->first();
            $budget->statuses()->attach($status->id, [
                'creator_id' => Auth::user()->id,
                'create_date' => now(),
            ]);
            DB::commit();
            return response()->json(['message' => 'وضعیت بودجه با موفقیت تغییر یافت'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error'], 500);
        }

    }

    public function declineBudget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'budgetID' => 'required',
            'description' => 'required',
            'fileID' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        try {
            $budget = Budget::find($request->budgetID);
            if (is_null($budget)) {
                return response()->json(['error' => 'بودجه مورد نظر یافت نشد'], 404);
            }
            DB::beginTransaction();
            $canceledStatus = BudgetStatusEnum::CANCELED->value;
            $status = Budget::GetAllStatuses()->where('name', $canceledStatus)->first();
            $budget->statuses()->attach($status->id, [
                'creator_id' => Auth::user()->id,
                'create_date' => now(),
                'description' => $request->description,
                'file_id' => $request->fileID,
            ]);
            $newBudget = $budget->replicate();
            $newBudget->parent_id = $budget->id;
            $newBudget->create_date = now();
            $newBudget->save();
            $status = $this->proposedBudgetStatus();
            BudgetStatus::create([
                'budget_id' => $newBudget->id,
                'status_id' => $status->id,
                'creator_id' => Auth::user()->id,
                'create_date' => now(),
            ]);
            $items = $budget->budgetItems;
            $newItems = $items->map(function ($item) use ($newBudget) {
                return [
                    'budget_id' => $newBudget->id,
                    'circular_item_id' => $item->circular_item_id,
                    'proposed_amount' => $item->proposed_amount,
                    'finalized_amount' => 0,
                    'percentage' => $item->percentage,
                ];
            });
            BudgetItem::insert($newItems->toArray());
            DB::commit();
            return response()->json(['message' => 'بودجه با موفقیت لغو شد', 'data' => $newBudget], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error'], 500);
        }

    }

    public function insertSupplementaryBudget(Request $request)
    {
        $data = $request->all();
        $validator = \Validator::make($data, [
            'budgetID' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        try {
            DB::beginTransaction();
            $budget = Budget::with('budgetItems')->find($request->budgetID);
            if (is_null($budget)) {
                return response()->json(['error' => 'بودجه مورد نظر یافت نشد'], 404);
            }
            $newBudget = $budget->replicate();
            $newBudget->isSupplementary = true;
            $newBudget->parent_id = $budget->id;
            $newBudget->create_date = now();
            $newBudget->save();
            $status = $this->proposedBudgetStatus();
            BudgetStatus::create([
                'budget_id' => $newBudget->id,
                'status_id' => $status->id,
                'creator_id' => Auth::user()->id,
                'create_date' => now(),
            ]);
            $items = $budget->budgetItems;
            $newItems = $items->map(function ($item) use ($newBudget) {
                return [
                    'budget_id' => $newBudget->id,
                    'circular_item_id' => $item->circular_item_id,
                    'proposed_amount' => $item->proposed_amount,
                    'finalized_amount' => 0,
                    'percentage' => $item->percentage,
                ];
            });
            BudgetItem::insert($newItems->toArray());


            DB::commit();
            return response()->json(['message' => 'با موفقیت بودجه متمم ایجاد شد', 'data' => $newBudget], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error'], 500);
        }
    }

    public function tafrighBudget(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'budgetID' => 'required',
            'logType' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        try {
            $budget = Budget::joinRelationship('ounitFiscalYear.fiscalYear')
                ->addSelect([
                    'fiscal_years.id as fiscal_year_id',
                    'ounit_fiscalYear.ounit_id as ounit_id',
                ])
                ->find($data['budgetID']);

            $operationalType = SubjectTypeEnum::OPERATIONAL_EXPENSE->value;
            $economicType = SubjectTypeEnum::ECONOMIC_EXPENSE->value;
            $incomeType = SubjectTypeEnum::INCOME->value;
            $select = [
                'bgt_circular_subjects.code as code',
                'bgt_circular_subjects.name as name',
                'bgt_circular_subjects.id',
                'bgt_circular_subjects.parent_id',
                'next_year_budget_item.id as next_year_budget_item_id',
                'next_year_budget_item.proposed_amount as next_year_proposed_amount',
                'next_year_budget_item.percentage as next_year_percentage',

            ];
            if ($request->logType == 'expense') {
                $types = [$operationalType, $economicType];
                $select[] = DB::raw("SUM(COALESCE(CASE WHEN bgt_circular_subjects.subject_type_id = {$operationalType} THEN acc_articles.credit_amount ELSE 0 END, 0)) - SUM(COALESCE(CASE WHEN bgt_circular_subjects.subject_type_id = {$operationalType} THEN acc_articles.debt_amount ELSE 0 END, 0)) as total_operational_amount");

                $select[] = DB::raw("SUM(COALESCE(CASE WHEN bgt_circular_subjects.subject_type_id = {$economicType} THEN acc_articles.credit_amount ELSE 0 END, 0)) - SUM(COALESCE(CASE WHEN bgt_circular_subjects.subject_type_id = {$economicType} THEN acc_articles.debt_amount ELSE 0 END, 0)) as total_economic_amount");

            } else {
                $types = [$incomeType];
                $select[] = DB::raw("SUM(COALESCE(acc_articles.credit_amount,0)) - SUM(COALESCE(acc_articles.debt_amount,0)) as total_amount");
            }

            $subjectsWithLog = CircularSubject::withoutGlobalScopes()
                ->with(['ancestors' => function ($query) {
                    $query->withoutGlobalScopes();
                }])
                ->whereIntegerInRaw('bgt_circular_subjects.subject_type_id', $types)
                ->leftJoinRelationship('account',
                    function ($join) use ($budget) {
                        $join->leftJoin('acc_articles', 'acc_articles.account_id', '=', 'acc_accounts.id')
                            ->join('acc_documents', function ($join) use ($budget) {
                                $join->on('acc_articles.document_id', '=', 'acc_documents.id')
                                    ->where('acc_documents.document_type_id', '=', DocumentTypeEnum::NORMAL->value)
                                    ->where('fiscal_year_id', $budget?->fiscal_year_id)
                                    ->where('acc_documents.ounit_id', $budget->ounit_id);
                            })
                            ->join('accDocument_status', function ($join) {
                                $join->on('accDocument_status.document_id', '=', 'acc_documents.id')
                                    ->whereRaw('accDocument_status.create_date = (
                                                 SELECT MAX(create_date)
                                                 FROM accDocument_status
                                                 WHERE document_id = acc_documents.id
                                             )');
                            })
                            ->join('statuses', 'statuses.id', '=', 'accDocument_status.status_id')
                            ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value);
                    }
                )
                ->whereNotIn('bgt_circular_subjects.id', function ($query) {
                    $query->select('bgt_circular_subjects.parent_id')
                        ->from('bgt_circular_subjects')
                        ->whereNotNull('bgt_circular_subjects.parent_id');
                })
                ->joinRelationshipUsingAlias('circularItem.budgetItem', [
                    'budgetItem' => function ($join) use ($budget) {
                        $join->as('next_year_budget_item')
                            ->where('budget_id', $budget->id);
                    },
                ])
                ->select($select)
                ->groupBy(
                    'bgt_circular_subjects.code',
                    'bgt_circular_subjects.name',
                    'bgt_circular_subjects.id',
                    'bgt_circular_subjects.parent_id',
                    'next_year_budget_item.id',
                    'next_year_budget_item.proposed_amount',
                    'next_year_budget_item.percentage'
                )
                ->get();
            if ($request->logType == 'expense') {
                return TafriqBudgetExpanse::collection($subjectsWithLog);

            }
            return TafriqBudgetIncome::collection($subjectsWithLog);

        } catch (\Exception $e) {
            return response()->json(['error' => 'error', 'trace' => 'error'], 500);
        }

    }
}
