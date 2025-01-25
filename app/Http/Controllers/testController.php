<?php

namespace App\Http\Controllers;


use Modules\ACMS\app\Http\Enums\BudgetStatusEnum;
use Modules\ACMS\app\Http\Enums\SubjectTypeEnum;
use Modules\ACMS\app\Http\Trait\BudgetItemsTrait;
use Modules\ACMS\app\Http\Trait\BudgetTrait;
use Modules\ACMS\app\Http\Trait\CircularTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\ACMS\app\Http\Trait\OunitFiscalYearTrait;
use Modules\ACMS\app\Models\Budget;
use Modules\ACMS\app\Models\CircularSubject;
use Modules\ACMS\app\Models\FiscalYear;
use Morilog\Jalali\Jalalian;


class testController extends Controller
{
    use FiscalYearTrait, CircularTrait, OunitFiscalYearTrait, BudgetTrait, BudgetItemsTrait;

    public function run()
    {
//        dd('1404' - 1, '1404' - 2,);
        $a = Jalalian::fromFormat('Y/m/d', '1403/10/01');
        $startOfQuarter = $a->getEndDayOfQuarter()->toCarbon()->startOfDay()->toDateTimeString();

        $endOfQuarter = $a->getEndDayOfQuarter()->toCarbon()->endOfDay()->toDateTimeString();
//        dd($a->getEndDayOfQuarter());

        $startOfYear = $a->getFirstDayOfYear()->toCarbon()->startOfDay()->toDateTimeString();
        $endOfTheYear = $a->getEndDayOfYear()->toCarbon()->endOfDay()->toDateTimeString();

        $fiscalYear = FiscalYear::where('name', '1402')->first();

        $fiscalYear2 = FiscalYear::where('name', '1403')->first();
        $b = Budget::
        joinRelationship('ounitFiscalYear.fiscalYear')
            ->where('fiscal_years.name', '1403')
            ->where('ounit_fiscalYear.ounit_id', 5)
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


        $a = CircularSubject::withoutGlobalScopes()
            ->with(['ancestors' => function ($query) {
                $query->withoutGlobalScopes();
            }])
            ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::OPERATIONAL_EXPENSE->value)
            ->leftJoinRelationship('accounts.articles.document', [
                'accounts' => function ($join) {
                    $join->where('ounit_id', 5);
                },
                'document' => function ($join) use ($fiscalYear) {
                    $join->where('fiscal_year_id', $fiscalYear->id);
                },
            ])
            ->leftJoinRelationshipUsingAlias('accounts.articles.document', [
                'accounts' => function ($join) {
                    $join->as('3_months_last_year_acc')
                        ->where('ounit_id', 5);
                },
                'document' => function ($join) use ($fiscalYear) {
                    $join->as('3_months_last_year_doc')
                        ->where('fiscal_year_id', $fiscalYear->id);
                },
            ])
            ->leftJoinRelationshipUsingAlias('accounts.articles.document', [
                'accounts' => function ($join) {
                    $join->as('3_months_last_year_acc')
                        ->where('ounit_id', 5);
                },
                'document' => function ($join) use ($startOfQuarter, $endOfQuarter) {
                    $join->as('3_months_last_year_doc')
                        ->whereBetween('3_months_last_year_doc.document_date', [$startOfQuarter, $endOfQuarter]);
                },
            ])
            ->leftJoinRelationshipUsingAlias('accounts.articles.document', [
                'accounts' => function ($join) {
                    $join->as('9_months_current_year_acc')
                        ->where('ounit_id', 5);
                },
                'document' => function ($join) use ($startOfQuarter, $endOfQuarter) {
                    $join->as('9_months_current_year_doc')
                        ->whereBetween('9_months_current_year_doc.document_date', [$startOfQuarter, $endOfQuarter]);
                },
            ])
            ->whereNotIn('bgt_circular_subjects.id', function ($query) {
                $query->select('bgt_circular_subjects.parent_id')
                    ->from('bgt_circular_subjects')
                    ->whereNotNull('bgt_circular_subjects.parent_id');
            })
            ->leftJoinRelationship('circularItem.budgetItem', [
//                'circularItem' => function ($join) {
//                    $join->where('bgt_circular_items.circular_id', 13);
//                },
                'budgetItem' => function ($join) use ($b) {
                    $join->as('current_year_budget_item')
                        ->where('budget_id', $b->id);
                },
            ])
            ->joinRelationshipUsingAlias('circularItem.budgetItem', [
//                'circularItem' => function ($join) {
////                $join->as('next_year')
////                    $join->where('bgt_circular_items.circular_id', 5);
//                },
                'budgetItem' => function ($join) use ($fiscalYear) {
                    $join->as('next_year_budget_item')
                        ->where('budget_id', 4101);
                },
            ])
            ->select([
                \DB::raw('SUM(COALESCE(acc_articles.credit_amount,0)) - SUM(COALESCE(acc_articles.debt_amount,0)) as total_amount'),
                'bgt_circular_subjects.code as code',
                'bgt_circular_subjects.name as name',

                \DB::raw('SUM(COALESCE(current_year_budget_item.proposed_amount,0)) as current_year_proposed_amount'),
                'next_year_budget_item.id as next_year_budget_item_id',
                'next_year_budget_item.proposed_amount as next_year_proposed_amount',

            ])
            ->groupBy('bgt_circular_subjects.code', 'bgt_circular_subjects.name', 'next_year_budget_item.id', 'next_year_budget_item.proposed_amount')
            ->get();
        dump($a);

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
