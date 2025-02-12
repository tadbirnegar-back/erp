<?php

namespace App\Http\Controllers;


use Modules\ACC\app\Http\Enums\DocumentTypeEnum;
use Modules\ACMS\app\Http\Enums\BudgetStatusEnum;
use Modules\ACMS\app\Http\Enums\SubjectTypeEnum;
use Modules\ACMS\app\Models\Budget;
use Modules\ACMS\app\Models\CircularSubject;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\BNK\app\Http\Traits\BankTrait;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Http\Traits\TransactionTrait;
use Morilog\Jalali\Jalalian;


class testController extends Controller
{
    use BankTrait, ChequeTrait, TransactionTrait;

    public function run()
    {

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
            ->find(30434);

        if (is_null($budget)) {
            return response()->json(['error' => 'بودجه یافت نشد'], 404);
        }
        $nextYear = $budget->fiscal_year_name;
        $currentYear = $budget->fiscal_year_name - 1;
        $lastYear = $budget->fiscal_year_name - 2;

        $nextYearFiscal = FiscalYear::where('name', $nextYear)->first();
        $lastYearFiscal = FiscalYear::where('name', $lastYear)->first();

        $a = Jalalian::fromFormat('Y/m/d', $lastYear . '/10/01');
//        $b = Jalalian::fromFormat('Y/m/d', $lastYear . '/10/01');
        $lastYearStartOfQuarter = $a->toCarbon()->startOfDay()->toDateTimeString();

        $lastYearEndOfQuarter = $a->getEndDayOfQuarter()->toCarbon()->endOfDay()->toDateTimeString();
//        dd($lastYearStartOfQuarter, $lastYearEndOfQuarter, $lastYear);
        $startOfCurrentYear = Jalalian::fromFormat('Y/m/d', $currentYear . '/01/01')->toCarbon()->startOfDay()->toDateTimeString();
        $endOf9thMonthOfCurrentYear = Jalalian::fromFormat('Y/m/d', $currentYear . '/09/30')->toCarbon()->endOfDay()->toDateTimeString();
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
//=========================================================================================================
        \DB::enableQueryLog();
        $subjectsWithLog = CircularSubject::withoutGlobalScopes()
            ->with(['ancestors' => function ($query) {
                $query->withoutGlobalScopes();
            }])
            ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::INCOME->value)
            ->leftJoinRelationship('account',
                function ($join) use ($budget, $lastYearFiscal) {
                    $join->leftJoin('acc_articles', 'acc_articles.account_id', '=', 'acc_accounts.id')
                        ->join('acc_documents', function ($join) use ($lastYearFiscal, $budget) {
                            $join->on('acc_articles.document_id', '=', 'acc_documents.id')
                                ->where('acc_documents.document_type_id', '=', DocumentTypeEnum::NORMAL->value)
                                ->where('fiscal_year_id', $lastYearFiscal?->id)
                                ->where('acc_documents.ounit_id', $budget->ounit_id);
                        });
                }
            )
//            ->leftJoinRelationshipUsingAlias('accounts', function ($join) use ($lastYearStartOfQuarter, $lastYearEndOfQuarter, $budget) {
//                $join->as('3_months_last_year_acc')
//                    ->join('acc_articles as 3_months_last_year_art', '3_months_last_year_art.account_id', '=', '3_months_last_year_acc.id')
//                    ->join('acc_documents as 3_months_last_year_doc', function ($join) use ($lastYearStartOfQuarter, $lastYearEndOfQuarter, $budget) {
//                        $join->on('3_months_last_year_art.document_id', '=', '3_months_last_year_doc.id')
//                            ->where('3_months_last_year_doc.document_type_id', '=', DocumentTypeEnum::NORMAL->value)
//                            ->where('3_months_last_year_doc.ounit_id', $budget->ounit_id)
//                            ->whereBetween('3_months_last_year_doc.document_date', [$lastYearStartOfQuarter, $lastYearEndOfQuarter]);
//                    });
//            }
//            )
//            ->leftJoinRelationshipUsingAlias('account',
//                function ($join) use ($startOfCurrentYear, $endOf9thMonthOfCurrentYear, $budget) {
//                    $join->as('9_months_current_year_acc')
//                        ->join('acc_articles as 9_months_last_year_art', '9_months_last_year_art.account_id', '=', '9_months_current_year_acc.id')
//                        ->join('acc_documents as 9_months_current_year_doc', function ($join) use ($startOfCurrentYear, $endOf9thMonthOfCurrentYear, $budget) {
//                            $join->on('9_months_last_year_art.document_id', '=', '9_months_current_year_doc.id')
//                                ->where('9_months_current_year_doc.document_type_id', '=', DocumentTypeEnum::NORMAL->value)
//                                ->where('9_months_current_year_doc.ounit_id', $budget->ounit_id)
//                                ->whereBetween('9_months_current_year_doc.document_date', [$startOfCurrentYear, $endOf9thMonthOfCurrentYear]);
//                        });
//                }
//            )
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

                \DB::raw('SUM(COALESCE(acc_articles.credit_amount,0)) - SUM(COALESCE(acc_articles.debt_amount,0)) as total_amount'),

                \DB::raw('SUM(COALESCE(current_year_budget_item.proposed_amount,0)) as current_year_proposed_amount'),

//                \DB::raw('SUM(COALESCE(3_months_last_year_art.credit_amount,0)) - SUM(COALESCE(3_months_last_year_art.debt_amount,0)) as three_months_last_year_proposed_amount'),

//                \DB::raw('SUM(COALESCE(9_months_last_year_art.credit_amount,0)) - SUM(COALESCE(9_months_last_year_art.debt_amount,0)) as nine_months_current_year_proposed_amount'),

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
        $threeMonthsLastYear = CircularSubject::withoutGlobalScopes()
            ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::INCOME->value)
            ->leftJoinRelationshipUsingAlias('accounts', function ($join) use ($lastYearStartOfQuarter, $lastYearEndOfQuarter, $budget) {
                $join->as('3_months_last_year_acc')
                    ->leftJoin('acc_articles as 3_months_last_year_art', '3_months_last_year_art.account_id', '=', '3_months_last_year_acc.id')
                    ->join('acc_documents as 3_months_last_year_doc', function ($join) use ($lastYearStartOfQuarter, $lastYearEndOfQuarter, $budget) {
                        $join->on('3_months_last_year_art.document_id', '=', '3_months_last_year_doc.id')
                            ->where('3_months_last_year_doc.document_type_id', '=', DocumentTypeEnum::NORMAL->value)
                            ->where('3_months_last_year_doc.ounit_id', $budget->ounit_id)
                            ->whereBetween('3_months_last_year_doc.document_date', ['2023-12-22 00:00:00', '2024-03-19 23:59:59']);
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

        $nineMonthsLastYear = CircularSubject::withoutGlobalScopes()
            ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::INCOME->value)
            ->leftJoinRelationshipUsingAlias('account',
                function ($join) use ($startOfCurrentYear, $endOf9thMonthOfCurrentYear, $budget) {
                    $join->as('9_months_current_year_acc')
                        ->leftJoin('acc_articles as 9_months_last_year_art', '9_months_last_year_art.account_id', '=', '9_months_current_year_acc.id')
                        ->join('acc_documents as 9_months_current_year_doc', function ($join) use ($startOfCurrentYear, $endOf9thMonthOfCurrentYear, $budget) {
                            $join->on('9_months_last_year_art.document_id', '=', '9_months_current_year_doc.id')
                                ->where('9_months_current_year_doc.document_type_id', '=', DocumentTypeEnum::NORMAL->value)
                                ->where('9_months_current_year_doc.ounit_id', $budget->ounit_id)
                                ->whereBetween('9_months_current_year_doc.document_date', [$startOfCurrentYear, $endOf9thMonthOfCurrentYear]);
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


                \DB::raw('SUM(COALESCE(9_months_last_year_art.credit_amount,0)) - SUM(COALESCE(9_months_last_year_art.debt_amount,0)) as nine_months_current_year_proposed_amount'),

            ])
            ->groupBy('bgt_circular_subjects.code', 'bgt_circular_subjects.name',
                'bgt_circular_subjects.id',
                'bgt_circular_subjects.parent_id',

            )
            ->get();
// Index by circular subject ID
        $subjects = $subjectsWithLog->keyBy('id');
        $threeMonths = $threeMonthsLastYear->keyBy('id');
        $nineMonths = $nineMonthsLastYear->keyBy('id');

// Merge the aggregates into the main subjects collection
        $finalResults = $subjects->map(function ($subject) use ($threeMonths, $nineMonths) {
            // If a subject is missing in the 3-month or 9-month data, default to 0
            $subject->three_months_last_year_proposed_amount = $threeMonths->has($subject->id)
                ? $threeMonths->get($subject->id)->three_months_last_year_proposed_amount
                : 0;
            $subject->nine_months_current_year_proposed_amount = $nineMonths->has($subject->id)
                ? $nineMonths->get($subject->id)->nine_months_current_year_proposed_amount
                : 0;
            return $subject;
        })->values();

        dump(\DB::getQueryLog(), $finalResults->where('three_months_last_year_proposed_amount', '!=', 0), $subjectsWithLog, $threeMonthsLastYear, $nineMonthsLastYear);


        $output = "<!DOCTYPE html>
    <html>
    <head>
        <title>Test Debugbar</title>
    </head>
    <body>
    </body></html>";


        echo $output;

    }
}
