<?php

namespace App\Http\Controllers;


class testController extends Controller
{

//    use PaymentRepository, ApprovingListTrait, EnactmentTrait, MeetingMemberTrait, RecruitmentScriptTrait, MeetingTrait;
//    use ExamsTrait;

    public function run()
    {
//        dd(111111);
    }
//
//        $r = Content::with('consumeLog')->find(7);
//        return response()->json($r);

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


//    }
}
