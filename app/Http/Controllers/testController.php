<?php

namespace App\Http\Controllers;


use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Modules\AAA\app\Models\User;
use Modules\ACMS\app\Http\Trait\BudgetItemsTrait;
use Modules\ACMS\app\Http\Trait\BudgetTrait;
use Modules\ACMS\app\Http\Trait\CircularTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\ACMS\app\Http\Trait\OunitFiscalYearTrait;
use Modules\ACMS\app\Jobs\DispatchCircularForOunitJob;
use Modules\ACMS\app\Models\Circular;
use Modules\ACMS\app\Models\FiscalYear;
use Morilog\Jalali\Jalalian;
use Throwable;


class testController extends Controller
{
    use FiscalYearTrait, CircularTrait, OunitFiscalYearTrait, BudgetTrait, BudgetItemsTrait;

    public function run()
    {
        $finishDate = convertJalaliPersianCharactersToGregorian('۱۴۰۳/۱۰/۲۴');
        dd($finishDate);
//        Document::powerJoin

        $currentYear = Jalalian::now()->getYear();
        $fiscalYear = FiscalYear::where('name', '1403')->first();
        $circular = Circular::with('fiscalYear', 'circularItems')->find(13);
        $user = User::find(1905);
        \DB::beginTransaction();
        $includedOunitsForBudget = $this->ounitsIncludingForAddingBudget($circular->fiscalYear->id, false)
            ->chunk(150);
        $fiscalYear = $circular->fiscalYear;

        $jobs = [];
        $includedOunitsForBudget->each(function ($chunkedUnits, $key) use ($fiscalYear, $user, $circular, &$jobs) {
            $chunkedUnits = $chunkedUnits->values();

            $jobs[] = new DispatchCircularForOunitJob($chunkedUnits->toArray(), $circular, $user);
        });
        $a = Bus::batch($jobs)
            ->then(function (Batch $batch) {
                // All jobs completed successfully
                \Log::info("All jobs in the batch have completed successfully.");
            })
            ->catch(function (Batch $batch, Throwable $e) {
                // Handle the exception
                \Log::error("An error occurred in the batch: " . $e->getMessage());
            })
            ->finally(function (Batch $batch) {
                // This block runs regardless of success or failure
                \Log::info("Batch processing is complete.");
            })
            ->name('DispatchCircularForOunitJob')
            ->onQueue('default')
            ->dispatch();
        $this->circularStatusAttach([
            'userID' => $user->id,
            'statusID' => $this->approvedCircularStatus()->id,

        ], $circular);
        \DB::commit();
        dd($a);
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
