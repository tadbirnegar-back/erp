<?php

namespace Modules\ACMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\ACC\app\Http\Enums\DocumentStatusEnum;
use Modules\ACC\app\Http\Enums\DocumentTypeEnum;
use Modules\ACMS\app\Http\Enums\BudgetStatusEnum;
use Modules\ACMS\app\Http\Enums\SubjectTypeEnum;
use Modules\ACMS\app\Models\Budget;
use Modules\ACMS\app\Models\BudgetItem;
use Modules\ACMS\app\Models\CircularSubject;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\ACMS\app\Resources\BudgetItemsForMain;
use Modules\ACMS\app\Resources\BudgetItemsForSupplementary;
use Morilog\Jalali\Jalalian;

class BudgetItemController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();

        $validator = \Validator::make($data, [
            'budgetID' => 'required',
            'subjectTypeID' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $budget = Budget::joinRelationship('ounitFiscalYear.fiscalYear')
            ->joinRelationship('statuses', [
                'statuses' => function ($join) {
                    $join
                        ->whereRaw('bgtBudget_status.create_date = (SELECT MAX(create_date) FROM bgtBudget_status WHERE budget_id = bgt_budgets.id)');
                }
            ])
            ->addSelect([
                'statuses.name as status_name',
                'fiscal_years.id as fiscal_year_id',
                'fiscal_years.name as fiscal_year_name',
                'ounit_fiscalYear.ounit_id as ounit_id',
            ])
            ->find($data['budgetID']);

        if (is_null($budget)) {
            return response()->json(['error' => 'بودجه یافت نشد'], 404);
        }
        $nextYear = $budget->fiscal_year_name;
        $currentYear = $budget->fiscal_year_name - 1;
        $lastYear = $budget->fiscal_year_name - 2;

        $nextYearFiscal = FiscalYear::where('name', $nextYear)->first();
        $lastYearFiscal = FiscalYear::where('name', $lastYear)->first();
        $currentYearFiscal = FiscalYear::where('name', $currentYear)->first();

        $a = Jalalian::fromFormat('Y/m/d', $lastYear . '/10/01');
        $lastYearStartOfQuarter = $a->toCarbon()->startOfDay()->toDateTimeString();

        $lastYearEndOfQuarter = $a->getEndDayOfQuarter()->toCarbon()->endOfDay()->toDateTimeString();
        $startOfCurrentYear = Jalalian::fromFormat('Y/m/d', $currentYear . '/01/01')->toCarbon()->startOfDay()->toDateTimeString();
        $endOf9thMonthOfCurrentYear = Jalalian::fromFormat('Y/m/d', $currentYear . '/09/30')->toCarbon()->endOfDay()->toDateTimeString();
        if ($budget->isSupplementary) {
            $currentYearBudget = $budget->parent;
        } else {
            $currentYearBudget = Budget::
            joinRelationship('ounitFiscalYear.fiscalYear')
                ->where('fiscal_years.name', $currentYear)
                ->where('ounit_fiscalYear.ounit_id', $budget->ounit_id)
                ->joinRelationship('statuses', [
                    'statuses' => function ($join) {
                        $join
                            ->whereRaw('bgtBudget_status.create_date = (SELECT MAX(create_date) FROM bgtBudget_status WHERE budget_id = bgt_budgets.id)');
                    }
                ])
                ->where('statuses.name', BudgetStatusEnum::FINALIZED->value)
                ->whereNotIn('bgt_budgets.id', function ($query) {
                    $query->select('bgt_budgets.parent_id')
                        ->from('bgt_budgets')
                        ->whereNotNull('bgt_budgets.parent_id');
                })
                ->first();

        }

        if ($budget->isSupplementary) {
            $subjectsWithLog = CircularSubject::withoutGlobalScopes()
                ->with(['ancestors' => function ($query) {
                    $query->withoutGlobalScopes();
                }])
                ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::tryFrom($request->subjectTypeID)->value)
                ->whereNotIn('bgt_circular_subjects.id', function ($query) {
                    $query->select('bgt_circular_subjects.parent_id')
                        ->from('bgt_circular_subjects')
                        ->whereNotNull('bgt_circular_subjects.parent_id');
                })
                ->leftJoinRelationship('account',
                    function ($join) use ($budget, $nextYearFiscal) {
                        $join->leftJoin('acc_articles', 'acc_articles.account_id', '=', 'acc_accounts.id')
                            ->join('acc_documents', function ($join) use ($nextYearFiscal, $budget) {
                                $join->on('acc_articles.document_id', '=', 'acc_documents.id')
                                    ->where('acc_documents.document_type_id', '=', DocumentTypeEnum::NORMAL->value)
                                    ->where('fiscal_year_id', $nextYearFiscal->id)
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
//            JoinRelationship('accounts.articles.document', [
////                    'accounts' => function ($join) use ($budget) {
////                        $join->where('ounit_id', $budget->ounit_id);
////                    },
//                    'document' => function ($join) use ($nextYearFiscal, $budget) {
//                        $join->where('fiscal_year_id', $nextYearFiscal->id)
//                            ->where('acc_documents.ounit_id', $budget->ounit_id);
//                    },
//                ])
                ->joinRelationshipUsingAlias('circularItem.budgetItem', [
//                'circularItem' => function ($join) {
////                $join->as('next_year')
////                    $join->where('bgt_circular_items.circular_id', 5);
//                },
                    'budgetItem' => function ($join) use ($budget) {
                        $join->as('next_year_budget_item')
                            ->where('budget_id', $budget->id);
                    },
                ])
//                ->joinRelationshipUsingAlias('circularItem.budgetItem', [
//
//                    'budgetItem' => function ($join) use ($currentYearBudget) {
//                        $join->as('current_year_budget_item')
//                            ->where('budget_id', $currentYearBudget?->id);
//                    },
//                ])
                ->select([
                    'bgt_circular_subjects.code as code',
                    'bgt_circular_subjects.name as name',
                    'bgt_circular_subjects.id',
                    'bgt_circular_subjects.parent_id',

                    \DB::raw('SUM(COALESCE(acc_articles.credit_amount,0)) - SUM(COALESCE(acc_articles.debt_amount,0)) as total_amount'),

//                    \DB::raw('SUM(COALESCE(current_year_budget_item.proposed_amount,0)) as current_year_proposed_amount'),

                    'next_year_budget_item.id as next_year_budget_item_id',
                    'next_year_budget_item.proposed_amount as next_year_proposed_amount',
                    'next_year_budget_item.percentage as next_year_percentage',

                ])
                ->groupBy('bgt_circular_subjects.code', 'bgt_circular_subjects.name', 'bgt_circular_subjects.id',
                    'bgt_circular_subjects.parent_id', 'next_year_budget_item.id', 'next_year_budget_item.proposed_amount', 'next_year_budget_item.percentage')
                ->get();
//            return response()->json(DB::getQueryLog());

            $currentYearLog = CircularSubject::withoutGlobalScopes()
                ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::tryFrom($request->subjectTypeID)->value)
                ->whereNotIn('bgt_circular_subjects.id', function ($query) {
                    $query->select('bgt_circular_subjects.parent_id')
                        ->from('bgt_circular_subjects')
                        ->whereNotNull('bgt_circular_subjects.parent_id');
                })
                ->joinRelationshipUsingAlias('circularItem.budgetItem', [

                    'budgetItem' => function ($join) use ($currentYearBudget) {
                        $join->as('current_year_budget_item')
                            ->where('budget_id', $currentYearBudget?->id);
                    },
                ])
                ->select([
                    'bgt_circular_subjects.code as code',
                    'bgt_circular_subjects.name as name',
                    'bgt_circular_subjects.id',
                    'bgt_circular_subjects.parent_id',

                    \DB::raw('SUM(COALESCE(current_year_budget_item.proposed_amount,0)) as current_year_proposed_amount'),

                ])
                ->groupBy(
                    'bgt_circular_subjects.code',
                    'bgt_circular_subjects.name',
                    'bgt_circular_subjects.id',
                    'bgt_circular_subjects.parent_id'
                )
                ->get();


            $currentYearLogMap = $currentYearLog->keyBy('id');

// Iterate over $subjectsWithLog and add current_year_proposed_amount
            $finalResults = $subjectsWithLog->map(function ($subject) use ($currentYearLogMap) {
                // Find the corresponding current year log entry
                $currentYearEntry = $currentYearLogMap->get($subject->id);

                // If a corresponding entry exists, add current_year_proposed_amount
                if ($currentYearEntry) {
                    $subject->current_year_proposed_amount = $currentYearEntry->current_year_proposed_amount;
                } else {
                    $subject->current_year_proposed_amount = 0; // If no matching entry, set to 0 or any default value
                }

                return $subject;
            });
        } else {

            $subjectsWithLog = CircularSubject::withoutGlobalScopes()
                ->with(['ancestors' => function ($query) {
                    $query->withoutGlobalScopes();
                }])
                ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::tryFrom($request->subjectTypeID)->value)
                ->whereNotIn('bgt_circular_subjects.id', function ($query) {
                    $query->select('bgt_circular_subjects.parent_id')
                        ->from('bgt_circular_subjects')
                        ->whereNotNull('bgt_circular_subjects.parent_id');
                })
                ->leftJoinRelationship('circularItem.budgetItem', [

                    'budgetItem' => function ($join) use ($currentYearBudget) {
                        $join->as('current_year_budget_item')
                            ->where('budget_id', $currentYearBudget?->id);
                    },
                ])
                ->joinRelationshipUsingAlias('circularItem.budgetItem', [
                    'budgetItem' => function ($join) use ($budget) {
                        $join->as('next_year_budget_item')
                            ->where('budget_id', $budget->id);
                    },
                ])
                ->select([
                    'bgt_circular_subjects.code as code',
                    'bgt_circular_subjects.name as name',
                    'bgt_circular_subjects.id',
                    'bgt_circular_subjects.parent_id',

//                    \DB::raw('SUM(COALESCE(acc_articles.credit_amount,0)) - SUM(COALESCE(acc_articles.debt_amount,0)) as total_amount'),

                    \DB::raw('SUM(DISTINCT COALESCE(current_year_budget_item.proposed_amount,0)) as current_year_proposed_amount'),

                    'next_year_budget_item.id as next_year_budget_item_id',
                    'next_year_budget_item.proposed_amount as next_year_proposed_amount',
                    'next_year_budget_item.percentage as next_year_percentage',

                ])
                ->groupBy('bgt_circular_subjects.code', 'bgt_circular_subjects.name',
                    'bgt_circular_subjects.id',
                    'bgt_circular_subjects.parent_id',
                    'next_year_budget_item.id',
                    'next_year_budget_item.proposed_amount',
                    'next_year_budget_item.percentage')
                ->get();

            $lastYearConfirmedDocuments = Cache::remember("last_year_confirmed_documents_ounit_{$budget->ounit_id}_year_{$lastYearFiscal->id}_subject_type_{$request->subjectTypeID}", now()->addDays(3), function () use ($request, $budget, $lastYearFiscal) {
                return CircularSubject::withoutGlobalScopes()
                    ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::tryFrom($request->subjectTypeID)->value)
                    ->leftJoinRelationship('account',
                        function ($join) use ($budget, $lastYearFiscal) {
                            $join->leftJoin('acc_articles', 'acc_articles.account_id', '=', 'acc_accounts.id')
                                ->join('acc_documents', function ($join) use ($lastYearFiscal, $budget) {
                                    $join->on('acc_articles.document_id', '=', 'acc_documents.id')
                                        ->where('acc_documents.document_type_id', '=', DocumentTypeEnum::NORMAL->value)
                                        ->where('fiscal_year_id', $lastYearFiscal?->id)
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
                    ->select([
                        'bgt_circular_subjects.code as code',
                        'bgt_circular_subjects.name as name',
                        'bgt_circular_subjects.id',
                        'bgt_circular_subjects.parent_id',

                        \DB::raw('SUM(COALESCE(acc_articles.credit_amount,0)) - SUM(COALESCE(acc_articles.debt_amount,0)) as total_amount'),

                    ])
                    ->groupBy('bgt_circular_subjects.code', 'bgt_circular_subjects.name',
                        'bgt_circular_subjects.id',
                        'bgt_circular_subjects.parent_id',

                    )
                    ->get();
            });


            $threeMonthsLastYear = Cache::remember("three_months_two_years_ago_ounit_{$budget->ounit_id}_year_{$lastYearFiscal->id}_subject_type_{$request->subjectTypeID}", now()->addDays(3), function () use ($request, $budget, $lastYearFiscal, $lastYearStartOfQuarter, $lastYearEndOfQuarter) {

                return CircularSubject::withoutGlobalScopes()
                    ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::tryFrom($request->subjectTypeID)->value)
                    ->leftJoinRelationshipUsingAlias('accounts', function ($join) use ($lastYearStartOfQuarter, $lastYearEndOfQuarter, $budget) {
                        $join->as('3_months_last_year_acc')
                            ->leftJoin('acc_articles as 3_months_last_year_art', '3_months_last_year_art.account_id', '=', '3_months_last_year_acc.id')
                            ->join('acc_documents as 3_months_last_year_doc', function ($join) use ($lastYearStartOfQuarter, $lastYearEndOfQuarter, $budget) {
                                $join->on('3_months_last_year_art.document_id', '=', '3_months_last_year_doc.id')
                                    ->where('3_months_last_year_doc.document_type_id', '=', DocumentTypeEnum::NORMAL->value)
                                    ->where('3_months_last_year_doc.ounit_id', $budget->ounit_id)
                                    ->whereBetween('3_months_last_year_doc.document_date', [$lastYearStartOfQuarter, $lastYearEndOfQuarter])
                                    ->join('accDocument_status', function ($join) {
                                        $join->on('accDocument_status.document_id', '=', '3_months_last_year_doc.id')
                                            ->whereRaw('accDocument_status.create_date = (
                                                 SELECT MAX(create_date)
                                                 FROM accDocument_status
                                                 WHERE document_id = 3_months_last_year_doc.id
                                             )');
                                    })
                                    ->join('statuses', 'statuses.id', '=', 'accDocument_status.status_id')
                                    ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value);
                            });
                    }
                    )
                    ->whereNotIn('bgt_circular_subjects.id', function ($query) {
                        $query->select('bgt_circular_subjects.parent_id')
                            ->from('bgt_circular_subjects')
                            ->whereNotNull('bgt_circular_subjects.parent_id');
                    })
                    ->select([
                        'bgt_circular_subjects.code as code',
                        'bgt_circular_subjects.name as name',
                        'bgt_circular_subjects.id',
                        'bgt_circular_subjects.parent_id',

                        \DB::raw('SUM(COALESCE(3_months_last_year_art.credit_amount,0)) - SUM(COALESCE(3_months_last_year_art.debt_amount,0)) as three_months_last_year_proposed_amount'),


                    ])
                    ->groupBy('bgt_circular_subjects.code', 'bgt_circular_subjects.name',
                        'bgt_circular_subjects.id',
                        'bgt_circular_subjects.parent_id',

                    )
                    ->get();

            });

            $nineMonthsLastYear = Cache::remember("nine_month_last_year_ounit_{$budget->ounit_id}_year_{$currentYearFiscal->id}_subject_type_{$request->subjectTypeID}", now()->addDays(3), function () use ($request, $budget, $startOfCurrentYear, $endOf9thMonthOfCurrentYear) {
               return CircularSubject::withoutGlobalScopes()
                    ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::tryFrom($request->subjectTypeID)->value)
                    ->leftJoinRelationshipUsingAlias('account',
                        function ($join) use ($startOfCurrentYear, $endOf9thMonthOfCurrentYear, $budget) {
                            $join->as('9_months_current_year_acc')
                                ->leftJoin('acc_articles as 9_months_last_year_art', '9_months_last_year_art.account_id', '=', '9_months_current_year_acc.id')
                                ->join('acc_documents as 9_months_current_year_doc', function ($join) use ($startOfCurrentYear, $endOf9thMonthOfCurrentYear, $budget) {
                                    $join->on('9_months_last_year_art.document_id', '=', '9_months_current_year_doc.id')
                                        ->where('9_months_current_year_doc.document_type_id', '=', DocumentTypeEnum::NORMAL->value)
                                        ->where('9_months_current_year_doc.ounit_id', $budget->ounit_id)
                                        ->whereBetween('9_months_current_year_doc.document_date', [$startOfCurrentYear, $endOf9thMonthOfCurrentYear]);
                                })->join('accDocument_status', function ($join) {
                                    $join->on('accDocument_status.document_id', '=', '9_months_current_year_doc.id')
                                        ->whereRaw('accDocument_status.create_date = (
                                                 SELECT MAX(create_date)
                                                 FROM accDocument_status
                                                 WHERE document_id = 9_months_current_year_doc.id
                                             )');
                                })
                                ->join('statuses', 'statuses.id', '=', 'accDocument_status.status_id');
                        }
                    )
                    ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value)
                    ->whereNotIn('bgt_circular_subjects.id', function ($query) {
                        $query->select('bgt_circular_subjects.parent_id')
                            ->from('bgt_circular_subjects')
                            ->whereNotNull('bgt_circular_subjects.parent_id');
                    })
                    ->select([
                        'bgt_circular_subjects.code as code',
                        'bgt_circular_subjects.name as name',
                        'bgt_circular_subjects.id',
                        'bgt_circular_subjects.parent_id',


                        \DB::raw('SUM(COALESCE(9_months_last_year_art.credit_amount,0)) - SUM(COALESCE(9_months_last_year_art.debt_amount,0)) as nine_months_current_year_proposed_amount'),

                    ])
                    ->groupBy('bgt_circular_subjects.code', 'bgt_circular_subjects.name',
                        'bgt_circular_subjects.id',
                        'bgt_circular_subjects.parent_id',

                    )
                    ->get();

            });
// Index by circular subject ID
            $subjects = $subjectsWithLog->keyBy('id');
            $threeMonths = $threeMonthsLastYear->keyBy('id');
            $nineMonths = $nineMonthsLastYear->keyBy('id');
            $totalLastYear = $lastYearConfirmedDocuments->keyBy('id');
// Merge the aggregates into the main subjects collection
            $finalResults = $subjects->map(function ($subject) use ($threeMonths, $nineMonths, $totalLastYear) {
                // If a subject is missing in the 3-month or 9-month data, default to 0
                $subject->three_months_last_year_proposed_amount = $threeMonths->has($subject->id)
                    ? $threeMonths->get($subject->id)->three_months_last_year_proposed_amount
                    : 0;
                $subject->nine_months_current_year_proposed_amount = $nineMonths->has($subject->id)
                    ? $nineMonths->get($subject->id)->nine_months_current_year_proposed_amount
                    : 0;

                $subject->total_amount =
                    $totalLastYear->has($subject->id)
                        ? $totalLastYear->get($subject->id)->total_amount
                        : 0;
                return $subject;
            })->values();
        }
        if ($budget->isSupplementary) {
            return BudgetItemsForSupplementary::collection($finalResults)
                ->additional(['fiscal_year' => $budget->fiscal_year_name, 'status' => $budget->status_name]);
        }
        return BudgetItemsForMain::collection($finalResults)
            ->additional(['fiscal_year' => $budget->fiscal_year_name, 'status' => $budget->status_name]);

    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $validator = \Validator::make($data, [
                'budgetItems' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
            $decodedItems = json_decode($data['budgetItems'], true);

            foreach ($decodedItems as $decodedItem) {
                $budgetItem = BudgetItem::find($decodedItem['id']);
                $budgetItem->proposed_amount = $decodedItem['proposed_amount'];
                $budgetItem->save();
            }

            DB::commit();
            return response()->json(['message' => 'با موفقیت ویرایش شد'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error'], 500);
        }

    }

}
