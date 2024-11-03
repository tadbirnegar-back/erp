<?php

namespace App\Http\Controllers;


use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\EnactmentReviewEnum;
use Modules\EMS\app\Models\Meeting;
use Modules\Gateway\app\Http\Traits\PaymentRepository;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Models\RecruitmentScript;


class testController extends Controller
{
    use PaymentRepository, ApprovingListTrait;

    public function run()
    {
        $user = User::with('activeDistrictRecruitmentScript.ounit.ancestorsAndSelf')->find(2060);
        /**
         * @var RecruitmentScript $rs
         */
        $rs = $user->activeDistrictRecruitmentScript->first();

        $meetings = Meeting::where('ounit_id', '=', $rs->organization_unit_id)
            ->where('isTemplate', false)
            ->whereBetween('meeting_date', ['2020-01-01', '2024-12-29'])
            ->with(['enactments' => function ($q) {
                $q->with(['enactmentReviews' => function ($qq) {
                    $qq->with(['status']);
                }, 'title', 'latestMeeting', 'status']);
            }])
            ->with(['meetingMembers' => function ($query) {
                $query->with('mr', 'person.avatar');


            },])
            ->get();
        $membersResult = [];

        foreach ($meetings as $meeting) {
            foreach ($meeting->meetingMembers as $member) {

                $employeeId = $member->employee_id;

                // Initialize the employee's status count array if not already set
                if (!isset($membersResult[$employeeId])) {
                    $membersResult[$employeeId] = [
                        'person' => $member->person,
                    ];
                }

                $membersResult[$employeeId]['meeting_count'] = ($membersResult[$employeeId]['meeting_count'] ?? 0) + 1;

                foreach ($meeting->enactments as $enactment) {
                    // Find the review for this employee in the current enactment
                    $review = $enactment->enactmentReviews->firstWhere('user_id', $employeeId);

                    $membersResult[$employeeId]['enactment_count'] = ($membersResult[$employeeId]['enactment_count'] ?? 0) + 1;

                    if ($review) {
                        // Increment the count for the review's status
                        $status = $review->status->name;
                        $membersResult[$employeeId][$status] = ($membersResult[$employeeId][$status] ?? 0) + 1;
                    } else {
                        // If no review is found, increment 'در انتظار بررسی'
                        $membersResult[$employeeId]['در انتظار بررسی'] = ($membersResult[$employeeId]['در انتظار بررسی'] ?? 0) + 1;
                    }
                }
            }
        }
        $collection = collect($membersResult);

// Calculate sums
        $totalMeetingCount = $collection->sum('meeting_count');
        $totalEnactmentCount = $collection->sum('enactment_count');
        $totalMaghayert = $collection->sum(EnactmentReviewEnum::INCONSISTENCY->value);
        $totalAdamMaghayert = $collection->sum(EnactmentReviewEnum::NO_INCONSISTENCY->value);
        $totalAdamMaghayertAutomatic = $collection->sum(EnactmentReviewEnum::SYSTEM_NO_INCONSISTENCY->value);
        $totalInReview = $collection->sum('در انتظار بررسی');

        $response = [
            'totalMeetingCount' => $totalMeetingCount,
            'totalEnactmentCount' => $totalEnactmentCount,
            'totalMaghayert' => $totalMaghayert,
            'totalAdamMaghayert' => $totalAdamMaghayert,
            'totalAdamMaghayertAutomatic' => $totalAdamMaghayertAutomatic,
            'totalInReview' => $totalInReview,
            'members' => $collection,
            'organizationUnit' => $rs->ounit->ancestorsAndSelf,
            'expired_count' => $totalAdamMaghayertAutomatic,
            'approved_count' => $totalAdamMaghayert + $totalMaghayert,
        ];

// Output or process the final results
        dd($membersResult);
        $membersResult = collect();

        foreach ($meetings as $meeting) {
            foreach ($meeting->meetingMembers as $member) {
                $employeeId = $member->employee_id;

                // Collect all enactment reviews for this employee across all enactments in the meeting
                $reviewsByMember = $meeting->enactments
                    ->flatMap(fn($enactment) => $enactment->enactmentReviews)
                    ->where('user_id', $employeeId);

                // Group reviews by status and count each group
                $statusCounts = $reviewsByMember->groupBy('status.name')->map->count();

                // If no reviews, initialize or increment 'در انتظار بررسی' count by 1
                if ($reviewsByMember->isEmpty()) {
                    $statusCounts['در انتظار بررسی'] = $statusCounts->get('در انتظار بررسی', 0) + 1;
                }

                // Aggregate counts into totalStatusCounts for the employee
                if (!isset($membersResult[$employeeId])) {
                    $membersResult[$employeeId] = collect();
                }

                foreach ($statusCounts as $status => $count) {
                    $membersResult[$employeeId][$status] = $membersResult[$employeeId]->get($status, 0) + $count;
                }
            }
        }

// Convert the result to an array or keep as a collection, as needed
        $membersResult = $membersResult->map->all();

// Output or process the final results
        dd($membersResult);
        $results = $meetings->map(function ($meeting) {
            $meetingResult = $meeting->meetingMembers->map(function ($member) use ($meeting) {
                $employeeId = $member->employee_id;

                // Collect all enactment reviews for this employee across all enactments in the meeting
                $reviewsByMember = $meeting->enactments
                    ->flatMap(fn($enactment) => $enactment->enactmentReviews)
                    ->where('user_id', $employeeId);

                // Group reviews by status and count each group
                $statusCounts = $reviewsByMember->groupBy('status.name')->map->count();

                // If no reviews, add 'در انتظار بررسی' with count 1 or increment it if already present
                if ($reviewsByMember->isEmpty()) {
                    $statusCounts['در انتظار بررسی'] = $statusCounts->get('در انتظار بررسی', 0) + 1;
                }

                // Return each member's data with total review counts by status
                return [
                    'member_id' => $member->id,
                    'employee_id' => $employeeId,
                    'review_counts_by_status' => $statusCounts,
                ];
            });

            // Return each meeting's data with all members' results
            return [
                'meeting_id' => $meeting->id,
                'members' => $meetingResult,
            ];
        });

// Output or process $results as needed
        dd($results);
        $results = [];

        foreach ($meetings as $meeting) {
            $meetingResult = [];

            foreach ($meeting->meetingMembers as $member) {
                $employeeId = $member->employee_id;

                // Filter enactment reviews where user_id matches employee_id
                $reviewsByMember = $meeting->enactments->flatMap(function ($enactment) use ($employeeId) {
                    return $enactment->enactmentReviews->where('user_id', $employeeId);
                });

                // Group reviews by status and count each group
                $statusCounts = $reviewsByMember->groupBy('status.name')->map(function ($reviews) {
                    return $reviews->count();
                });

                // If no reviews, increment 'در انتظار بررسی' count by 1
                if ($statusCounts->isEmpty()) {
                    $statusCounts['در انتظار بررسی'] = 1;
                } else {
                    // Check if 'در انتظار بررسی' status exists and increment if it does
                    if ($statusCounts->has('در انتظار بررسی')) {
                        $statusCounts['در انتظار بررسی'] += 1;
                    }
                }

                // Store result for this member
                $meetingResult[] = [
                    'member_id' => $member->id,
                    'employee_id' => $employeeId,
                    'review_counts_by_status' => $statusCounts,
                ];
            }

            $results[] = [
                'meeting_id' => $meeting->id,
                'members' => $meetingResult,
            ];
        }

// Output or process $results as needed
        dd($results);
        $results = [];

        foreach ($meetings as $meeting) {
            $meetingResult = [];

            foreach ($meeting->meetingMembers as $member) {
                $employeeId = $member->employee_id;

                // Filter enactment reviews where user_id matches employee_id
                $reviewsByMember = $meeting->enactments->flatMap(function ($enactment) use ($employeeId) {
                    return $enactment->enactmentReviews->where('user_id', $employeeId);
                });

                // Group reviews by status and count each group
                $statusCounts = $reviewsByMember->groupBy('status.name')->map(function ($reviews) {
                    return $reviews->count();
                });

                // Store result for this member
                $meetingResult[] = [
                    'member_id' => $member->id,
                    'employee_id' => $employeeId,
                    'review_counts_by_status' => $statusCounts,
                ];
            }

            $results[] = [
                'meeting_id' => $meeting->id,
                'members' => $meetingResult,
            ];
        }

// Output or process $results as needed
        dd($results);
        dd($meetings);


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
