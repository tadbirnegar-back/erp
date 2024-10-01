<?php

namespace App\Http\Controllers;


use DB;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\EnactmentReviewEnum;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\EnactmentReview;
use Modules\Gateway\app\Http\Traits\PaymentRepository;
use Modules\HRMS\app\Http\Traits\EmployeeTrait;
use Modules\OUnitMS\app\Models\OrganizationUnit;


class testController extends Controller
{
    use PaymentRepository, EmployeeTrait;

    public function run()
    {


//        $users = User::whereHas('recruitmentScripts', function ($q) use ($ounit) {
//            $q->whereIntegerInRaw('organization_unit_id', $ounit);
//        })->with('person.avatar')->get();
//        dd($users);

//        $enactment = Enactment::with('consultingMembers')->find(3);
//        dd($enactment);
//        $user = User::find(1905);
//        $componentsToRenderWithData = $this->enactmentShow($enactment, $user);
//        dd($componentsToRenderWithData);


//        $enactments = Enactment::whe

        $startDate = '2020-01-01';
        $endDate = '2024-12-30';
        $meetingCount = Enactment::whereHas('members', function ($q) {
            $q->where('employee_id', 1905);
        })
            ->select('meeting_id', DB::raw('count(*) as enactment_count'))
            ->whereBetween('create_date', [$startDate, $endDate])
            ->groupBy('meeting_id')
            ->get();
//        dd($meetingCount);


        $myEnactmentCount = Enactment::whereHas('members', function ($q) {
            $q->where('employee_id', 1905);
        })
            ->whereBetween('create_date', [$startDate, $endDate])
            ->paginate();
//        dd($myEnactmentCount);

//        $enReviews = EnactmentReview::whereHas('enactment', function ($q) {
//            $q->whereBetween('create_date', ['2020-01-01', '2024-12-30']);
//        })
//            ->with('status')
//            ->where('user_id', 1905)
//            ->groupBy('status_id')
//            ->count();

        $enReviews = EnactmentReview::join('enactments', 'enactment_reviews.enactment_id', '=', 'enactments.id')
            ->whereBetween('enactments.create_date', [$startDate, $endDate])
            ->where('enactment_reviews.user_id', 1905)
            ->groupBy('enactment_reviews.status_id')
            ->select('enactment_reviews.status_id', DB::raw('count(*) as review_count'))
            ->with('status')
            ->get();
//        dd($enReviews);

        $enactmentMyReviewsCount = Enactment::whereHas('members', function ($q) {
            $q->where('employee_id', 1905);
        })
            ->whereBetween('create_date', [$startDate, $endDate])
            ->whereHas('enactmentReviews', function ($q) {
                $q->where('user_id', 1905);
            })
            ->count();

        $enactmentExpiredCount = Enactment::whereHas('members', function ($q) {
            $q->where('employee_id', 1905);
        })
            ->whereBetween('create_date', [$startDate, $endDate])
            ->whereHas('enactmentReviews.status', function ($q) {
                $q->where('name', EnactmentReviewEnum::SYSTEM_NO_INCONSISTENCY->value);
            })
            ->count();


        $enactmentUnreviewedCount = Enactment::whereHas('members', function ($q) {
            $q->where('employee_id', 1905);
        })
            ->whereBetween('create_date', [$startDate, $endDate])
            ->whereDoesntHave('enactmentReviews', function ($q) {
                $q->where('user_id', 1905);
            })
            ->count();
//        dd($enactmentUnreviewedCount);

        //==================================================================================

        $a = OrganizationUnit::with(['meetingMembers'])->find(2662);
        dd($a);
        User::with(['activeDistrictRecruitmentScript' => function ($q) {
            $q->orderByDesc('recruitment_scripts.create_date')
                ->limit(1)
                ->with('organizationUnit.descendantsAndSelf');
        }])->find(1905)//            ->activeRecruitmentScript[0]?->organizationUnit->descendantsAndSelf->pluck('id')
        ;


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
//             $html .= '<td>' . htmlspecialchars($organizationUnit->head->person->display_name) . '</td>';
//             $html .= '<td>' . htmlspecialchars($organizationUnit->ancestors[2]->name) . '</td>';
//             $html .= '<td>' . htmlspecialchars($organizationUnit->unitable->abadi_code) . '</td>';
//             $html .= '</tr>';
//         }

// // End the table
//         $html .= '</table>';

// // Print the table
//         echo $html;


//         try {

//             $authority='A0000000000000000000000000005nxmv33n';
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
//                    1 => 400000,
//                    2 => 450000,
//                    3 => 500000,
//                    4 => 600000,
//                    5 => 650000,
//                    6 => 700000,
//                    default => 0,
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

//             return response()->json(['message' => 'درصورت بروز مشکل با پشتیبانی تماس بگیرید'], 400);

//         }

    }
}
