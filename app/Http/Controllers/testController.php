<?php

namespace App\Http\Controllers;


use Modules\ACC\app\Http\Enums\DocumentStatusEnum;
use Modules\ACC\app\Models\Document;
use Modules\ACMS\app\Http\Trait\BudgetItemsTrait;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\Gateway\app\Http\Traits\PaymentRepository;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Morilog\Jalali\Jalalian;


class testController extends Controller
{
    use PaymentRepository, ApprovingListTrait, EnactmentTrait, MeetingMemberTrait, RecruitmentScriptTrait, MeetingTrait, BudgetItemsTrait;

    public function run()
    {
//        Document::powerJoin

        $currentYear = Jalalian::now()->getYear();
        $fiscalYear = FiscalYear::where('name', $currentYear)->first();
        \DB::enableQueryLog();
        $docs = Document::leftJoinRelationship('articles')
//            ->join('accDocument_status', 'acc_documents.id', '=', 'accDocument_status.document_id')
//            ->join('statuses', 'accDocument_status.status_id', '=', 'statuses.id')
//            ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value)
//            ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')

            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join
//                    ->on('accDocument_status.id', '=', \DB::raw('(
//                    SELECT id
//                    FROM accDocument_status AS ps
//                    WHERE ps.document_id = acc_documents.id
//                    ORDER BY ps.create_date DESC
//                    LIMIT 1
//                )'))
//                    ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value)
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value)//                    ->orderBy('accDocument_status.create_date', 'desc')

                ;
            }])
            ->where('acc_documents.ounit_id', 2748)
            ->where('acc_documents.fiscal_year_id', $fiscalYear->id)
            ->select([
                'acc_documents.id as id',
                'acc_documents.description as document_description',
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'acc_documents.document_date as document_date',
                'acc_documents.document_number as document_number',
                'acc_documents.create_date as create_date',
                \DB::raw('SUM(acc_articles.debt_amount) as total_debt_amount'),
                \DB::raw('SUM(acc_articles.credit_amount) as total_credit_amount'),
            ])
            ->groupBy('acc_documents.id', 'acc_documents.description', 'acc_documents.document_date', 'acc_documents.document_number', 'acc_documents.create_date', 'statuses.name', 'statuses.class_name')
//            ->with('latestStatus')
            ->get();
        dd($docs, \DB::getQueryLog());

//        $budget = BudgetItem::joinRelationship('circularItem.subject')
//            ->where('budget_id', 4774)
//            ->where('bgt_circular_subjects.subject_type_id', SubjectTypeEnum::EXPENSE->value)
//            ->select([
//                'bgt_budget_items.id as item_id',
//                'bgt_budget_items.finalized_amount as approved_amount',
//                'bgt_budget_items.percentage as percentage',
//                'bgt_budget_items.proposed_amount as proposed_amount',
//                'bgt_circular_subjects.id as id', 'bgt_circular_subjects.name as name',
//                'bgt_circular_subjects.id as id', 'bgt_circular_subjects.parent_id as parent_id',
//            ])
//            ->get();
//        $budget->statuses = $budget->statuses->sortBy('pivot.create_date');
//        dump($budget->toHierarchy());
//        dump($budgets);
        $output = "<!DOCTYPE html>
    <html>
    <head>
        <title>Test Debugbar</title>
    </head>
    <body>
        <h1>Testing Debugbar Rendering</h1>
    </body>";

        $output .= "</html>";

        echo $output;
//        return VillageBudgetListResource::collection($budgets);
//        dd($a);
//        Debugbar::stopMeasure('render');
//        dd($a, DB::getQueryLog());
//        $organizationUnitIds = OrganizationUnit::where('unitable_type', VillageOfc::class)->with(['head.person.personable', 'head.person.workForce.educationalRecords.levelOfEducation', 'ancestorsAndSelf', 'unitable', 'ancestors' => function ($q) {
//            $q->where('unitable_type', DistrictOfc::class);
//
//        }, 'evaluators',
//            'payments' => function ($query) {
//                $query->where('status_id', '=', '46');
//            }])->get();
//
//
////
////        // Start the table
//        $html = '<table>';
////        $html .= '<tr><th>دهیاری</th><th>نام دهیار</th><th>شهرستان</th><th>کد آبادی</th></tr>';
//
//        // Loop through the data and add it to the table
//        foreach ($organizationUnitIds as $organizationUnit) {
//            $payCalculate = collect($this->calculatePrice(collect([$organizationUnit]))['ounits']);
//            $c = $payCalculate->map(function ($item) {
//                $phase1 = ($item['alreadyPayed'] > 0);
//                $phase2 = ($item['price'] <= 0 && $item['alreadyPayed'] > 0);
//
//                /**
//                 * @var OrganizationUnit $ou
//                 */
//                $ou = $item['ounitObject'];
//                $ou->setAttribute('payStat', [
//                    'phase_1' => $phase1,
//                    'phase_2' => $phase2,
//                ]);
//                return $ou;
//            });
//            $c = $c->first();
//            foreach ($organizationUnit->evaluators as $evaluator) {
//                $districteval = $evaluator->user_id == $organizationUnit->ancestors[0]->head_id ? $evaluator->sum : null;
//                $villageeval = $evaluator->user_id == $organizationUnit->head_id ? $evaluator->sum : null;
//            }
//            if (isset($organizationUnit->head?->person->personable->birth_date)) {
//
//                $jalali = \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($organizationUnit->head?->person->personable->birth_date));
//            } else {
//                $jalali = null;
//            }
//
//            $html .= '<tr>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->ancestorsAndSelf[0]->name) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->ancestorsAndSelf[1]->name) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->ancestorsAndSelf[2]->name) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->ancestorsAndSelf[3]->name) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->unitable->abadi_code) . '</td>';
//
//            $html .= '<td>' . htmlspecialchars($organizationUnit->head?->person->personable->first_name) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->head?->person->personable->last_name) . '</td>';
//
//            $html .= '<td>' . htmlspecialchars($organizationUnit->head?->person->national_code) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->head?->person->personable->bc_code) . '</td>';
//
//            $html .= '<td>' . htmlspecialchars($organizationUnit->head?->person->personable->father_name) . '</td>';
//            $html .= '<td>' . htmlspecialchars(isset($organizationUnit->head) ? ($organizationUnit->head?->person->personable->gender_id == 1 ? 'مرد' : 'زن') : null) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->head?->mobile) . '</td>';
//
//
//            $html .= '<td>' . htmlspecialchars($organizationUnit->head?->person->workForce->educationalRecords[0]?->field_of_study ?? null) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->head?->person->workForce->educationalRecords[0]?->levelOfEducation->name ?? null) . '</td>';
//            $html .= '<td>' . htmlspecialchars($jalali) . '</td>';
//
//            $html .= '<td>' . htmlspecialchars($villageeval) . '</td>';
//            $html .= '<td>' . htmlspecialchars($districteval) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->unitable->abadi_code) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->unitable->national_uid) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->unitable->hierarchy_code) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->unitable->ofc_code) . '</td>';
//
//            $html .= '<td>' . htmlspecialchars($c->payStat['phase_1'] ? 'پرداخت شده' : 'پرداخت نشده') . '</td>';
//            $html .= '<td>' . htmlspecialchars($c->payStat['phase_2'] ? 'پرداخت شده' : 'پرداخت نشده') . '</td>';
//
//            $html .= '</tr>';
//        }
//
//        // End the table
//        $html .= '</table>';
//
//        // Print the table
//        echo $html;


    }
}
