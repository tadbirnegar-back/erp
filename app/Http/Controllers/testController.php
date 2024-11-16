<?php

namespace App\Http\Controllers;


use Modules\Gateway\app\Http\Traits\PaymentRepository;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
<<<<<<< Updated upstream
use Modules\HRMS\app\Models\RecruitmentScript;
=======
use Modules\HRMS\app\Models\RecruitmentScriptStatus;
>>>>>>> Stashed changes


class testController extends Controller
{
    use PaymentRepository, ApprovingListTrait;

    public function run()
    {
<<<<<<< Updated upstream
        $a = RecruitmentScript::with('user')->find(1200);

        dd(
            $a
        );
=======
        RecruitmentScriptStatus::create([
            'recruitment_script_id' => 2738,
            'status_id' => 60
        ]);
//        $user = User::create([
//            'mobile' => '09146360241',
//            'password' => Hash::make('123456'),
//            'person_id' => 2132
//        ]);
//
//        return response()->json($user);
//        $a = RecruitmentScript::with('user')->find(1200);
//
//        dd(
//            $a
//        );
>>>>>>> Stashed changes

//        $organizationUnitIds = OrganizationUnit::where('unitable_type', VillageOfc::class)->with(['head.person.personable', 'head.person.workForce.educationalRecords.levelOfEducation', 'ancestorsAndSelf', 'unitable', 'ancestors' => function ($q) {
//            $q->where('unitable_type', DistrictOfc::class);
//
//        }, 'evaluators'])->get();
//        $organizationUnitIds = OrganizationUnit::where('unitable_type', VillageOfc::class)
//            ->with(['ancestorsAndSelf', 'ancestors' => function ($q) {
//                $q->where('unitable_type', DistrictOfc::class);
//
//            }, 'evaluators'])
//            ->get();
//
//        // Start the table
//        $html = '<table>';
//        $html .= '<tr>
//<th>دهیاری</th>
//<th>دهستان</th>
//<th>بخشداری</th>
//<th>شهرستان</th>
//<th>نمره نهایی دهیار</th>
//<th>نمره نهایی بخشدار</th>
//</tr>';
//
//        // Loop through the data and add it to the table
//        foreach ($organizationUnitIds as $organizationUnit) {
//            $districteval = null;
//            $villageeval = null;
//            foreach ($organizationUnit->evaluators as $evaluator) {
//                $districteval = $evaluator->user_id == $organizationUnit->ancestors[0]->head_id ? $evaluator->sum : null;
//                $villageeval = $evaluator->user_id == $organizationUnit->head_id ? $evaluator->sum : null;
//            }
//
//            $html .= '<tr>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->ancestorsAndSelf[0]->name) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->ancestorsAndSelf[1]->name) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->ancestorsAndSelf[2]->name) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->ancestorsAndSelf[3]->name) . '</td>';
//
//            $html .= '<td>' . htmlspecialchars($villageeval) . '</td>';
//            $html .= '<td>' . htmlspecialchars($districteval) . '</td>';
//
//
//            $html .= '</tr>';
//        }
//
//        // End the table
//        $html .= '</table>';
//
//        // Print the table
//        echo $html;
//
//        // Start the table
//        $html = '<table>';
//        $html .= '<tr><th>دهیاری</th><th>نام دهیار</th><th>شهرستان</th><th>کد آبادی</th></tr>';
//
//        // Loop through the data and add it to the table
//        foreach ($organizationUnitIds as $organizationUnit) {
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
//
//            $html .= '</tr>';
//        }
//
//        // End the table
//        $html .= '</table>';
//
//        // Print the table
//        echo $html;

// $user=User::with(['organizationUnits'])->find(1955);


// $a=$user->organizationUnits[0]->descendants()->whereDepth(2)->with(['person','ancestors','payments'=>function ($query) {
//     $query->where('status_id', '=', '46');
// },'evaluator'])->get();
//         // return response()->json($a);
//         dd($a->pluck('payments'));


//         $usersWithReadNotificationsButNoPaymentsWithStatus46 = User::whereHas('notifications'
// //  , function ($query) {
// //             $query->whereNotNull('read_at');
// //         }
//         )->whereDoesntHave('payments'
//             , function ($query) {
//                 $query->where('status_id', 46);
//             }
//         )->with('person','organizationUnits.ancestors','payments')->get();
//         // dd($usersWithReadNotificationsButNoPaymentsWithStatus46->pluck('id'));
//         $html = '<table>';
//         $html .= '<tr><th>دهیار</th><th>دهیاری</th><th>کد آبادی</th><th>شهرستان</th></tr>';

//         foreach ($usersWithReadNotificationsButNoPaymentsWithStatus46 as $user) {
//             foreach ($user->organizationUnits as $unit) {
//                 $html .= '<tr>';
//                 $html .= '<td>' . htmlspecialchars($user->person->display_name) . '</td>';
//                 $html .= '<td>' . htmlspecialchars($unit->name) . '</td>';
//                 $html .= '<td>' . htmlspecialchars($unit->unitable->abadi_code) . '</td>';
//                 $html .= '<td>' . htmlspecialchars($unit->ancestors[2]->name) . '</td>';
//                 $html .= '</tr>';
//             }
//             // If the user has no organization units, print just the user's name with "No Units"
//             if (count($user->organizationUnits) == 0) {
//                 $html .= '<tr><td>' . htmlspecialchars($user->person->display_name) . '</td><td>No Units</td></tr>';
//             }
//         }

//         $html .= '</table>';

//         echo $html;

//                 $organizationUnitIds = OrganizationUnit::whereHas('payments', function ($query) {
//             $query->where('status_id', 46)
//                 ->where('user_id','!=',1905);
//         })->with('head.person','ancestors','unitable')->get();

// // Start the table
//         $html = '<table>';
//         $html .= '<tr><th>دهیاری</th><th>نام دهیار</th><th>شهرستان</th><th>کد آبادی</th></tr>';

// // Loop through the data and add it to the table
//         foreach ($organizationUnitIds as $organizationUnit) {
//             $html .= '<tr>';
//             $html .= '<td>' . htmlspecialchars($organizationUnit->name) . '</td>';
//             $html .= '<td>' . htmlspecialchars($organizationUnit->head?->person->display_name) . '</td>';
//             $html .= '<td>' . htmlspecialchars($organizationUnit->ancestors[2]->name) . '</td>';
//             $html .= '<td>' . htmlspecialchars($organizationUnit->unitable->abadi_code) . '</td>';
//             $html .= '</tr>';
//         }

// // End the table
//         $html .= '</table>';

// // Print the table
//         echo $html;


//         try {


//             $authority='A000000000000000000000000000l35lv3nl';
//             $payment = PG::where('authority', $authority)->with('user')->first();

//             $user =$payment->user ;
//             $payments = $user->payments()->where('authority', $authority)->with('organizationUnit.unitable')->get();

//             $status = PG::GetAllStatuses()->where('name', 'پرداخت شده')->first();
//             $user->load(['organizationUnits' => function ($query) {
//                 $query->where('unitable_type', VillageOfc::class)->with('unitable');
//             }]);
//             $degs = $payments->pluck('organizationUnit.unitable.degree');
// //                ->reject(function ($dg) {
// //                return $dg === null;
// //            });

//             $amount = 0;
//             $degs->each(function ($deg) use (&$amount) {
//                 $deg = (int)$deg;

// //            $currentAmount = 0; // Initialize a variable for current increment
//                 $currentAmount = match ($deg) {
//                     1 => 400000,
//                     2 => 450000,
//                     3 => 500000,
//                     4 => 600000,
//                     5 => 650000,
//                     6 => 700000,
//                     default => 0,
//                 };

//                 $amount += $currentAmount;
//             });

//             $receipt = Payment::amount($amount)->transactionId($authority)->verify();

//             // You can show payment referenceId to the user.
//             $transactionid = $receipt->getReferenceId();

//             $payments->each(function ($payment) use ($transactionid, $receipt, $status) {
//                 $payment->transactionid = $transactionid;
//                 $payment->purchase_date = $receipt->getDate();
//                 $payment->status_id = $status->id;
//                 $payment->save();
//             });
//             $user->load('person');

//             $factor = [
//                 'transactionid' => $transactionid,
//                 'purchase_date' => $receipt->getDate(),
//                 'amount'=>$amount,
//                 'status'=>$status,
//                 'person' => $user->person,

//             ];

//             return response()->json(['data' => $factor, 'message' => 'پرداخت شما با موفقیت انجام شد']);

//         }
//         catch (InvalidPaymentException $exception) {
//             if ($exception->getCode() == 101) {
//                 $user?->load('person');
//                 $factor = [
//                     'transactionid' => $payments[0]->transactionid,
//                     'purchase_date' => $payments[0]->purchase_date,
//                     'amount'=>$amount,
//                     'status'=>$status,
//                     'person' => $user->person,

//                 ];
//                 return response()->json(['message' => $exception->getMessage(), 'data' => $factor ?? null]);
//             } elseif ($exception->getCode() == -51) {
//                 $status = PG::GetAllStatuses()->where('name', 'پرداخت ناموفق')->first();
//                 $payments->each(function ($payment) use ( $status) {
//                     $payment->status_id = $status->id;
//                     $payment->save();
//                 });

//             }

// return response()->json(['message' => 'درصورت بروز مشکل با پشتیبانی تماس بگیرید'], 400);

//         }

    }
}
