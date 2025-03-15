<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EVAL\app\Http\Traits\CircularTrait;
use Modules\EVAL\app\Http\Traits\EvaluationTrait;
use Modules\EVAL\app\Jobs\CircularExpirationJob;
use Modules\EVAL\app\Models\EvalCircular;
use Modules\EVAL\app\Models\EvalCircularStatus;
use Modules\EVAL\app\Models\EvalEvaluation;
use Modules\EVAL\app\Models\EvalEvaluationStatus;
use Modules\EvalMS\app\Models\Evaluator;
use Modules\Gateway\app\Http\Traits\PaymentRepository;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\LMS\app\Http\Traits\ExamsTrait;
use Modules\LMS\app\Models\Course;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Models\Person;

class testController extends Controller
{
    use PaymentRepository, ApprovingListTrait, EnactmentTrait, MeetingMemberTrait, RecruitmentScriptTrait, MeetingTrait;
    use ExamsTrait, EvaluationTrait, CircularTrait;

    public function run()
    {


















//        $circularId = 11;
//        $job = CircularExpirationJob::dispatch($circularId);
//
//        return response()->json([
//            'message' => 'Job has been dispatched successfully!',
//        ]);


//        $startTime = microtime(true);
//
////        $a = User::all()->random(4);
//
//        $a = User::inRandomOrder()->limit(4)->get();
//
//        $endTime = microtime(true);
//        $executionTime = $endTime - $startTime;
//
//        return response()->json([
//            'data' => $a,
//            'execution_time' => $executionTime . ' seconds'
//        ]);
//
//        $circularId = EvalCircular::find(11);
//
//        $evaluations = EvalEvaluation::where('eval_circular_id', $circularId->id)->get();
//
//        $date = Carbon::parse($circularId->expired_date);
//        if ( $date != null && $date->format('Y-m-d') == now()->format('Y-m-d')) {
//            EvalCircularStatus::create([
//                'eval_circular_id' => $circularId->id,
//                'status_id' => $this->expiredCircularStatus()->id,
//                'created_at' => now(),
//            ]);
//            foreach ($evaluations as $evaluation) {
//                EvalEvaluationStatus::create([
//                    'eval_evaluation_id' => $evaluation->id,
//                    'status_id' => $this->expiredCircularStatus()->id,
//                    'created_at' => now(),
//                    'creator_id' => $evaluation->creator_id,
//                ]);
//            }
        }


        //        $evals = EvalEvaluation::query()2025-03-12 10:44:50
//            ->joinRelationship('evalCircular.evalCircularStatus')
//            ->joinRelationship('evalEvaluationStatus')
//            ->where('eval_evaluations.eval_circular_id', $circularID)
//            ->whereNotNull('eval_evaluations.target_ounit_id')
//            ->where('eval_circular_statuses.status_id', $this->notifiedCircularStatus()->id)
//            ->where('evalEvaluation_status.status_id', $this->evaluationDoneStatus()->id)
//            ->count();
//
//        return response()->json($evals);


//        $searchTerm = 'ل';
//        $users = Person::query()
//            ->search('description', $searchTerm)
//            ->select([
//                'persons.display_name as name',
//                'persons.id as personID'
//            ])
//            ->get();
//        return response()->json($users);
        //Some Random Code to Test my larvel debug bar

//        $village = VillageOfc::query()
//            ->join('organization_units as ounits' , 'ounits.unitable_id' , '=' , 'village_ofcs.id')
//            ->join('recruitment_scripts as rss' , 'rss.organization_unit_id' , '=' , 'ounits.id')
////            ->whereIn('village_ofcs.id' , [1,2,3,4,5])
//            ->get();


//        $a = User::first();
//        dump($a);
//        $status = $this->questionActiveStatus();
//
//        $query = Course::joinRelationship('chapters.allActiveLessons.questions.difficulty')
//            ->joinRelationship('chapters.allActiveLessons.questions.options')
//            ->joinRelationship('chapters.allActiveLessons.questions.repository')
//            ->joinRelationship('chapters.allActiveLessons.questions.questionType')
//            ->select([
//                'questions.id as QID',
//                'lessons.title as lesson title'
//            ])->where('questions.status_id', $status->id)
//            ->get();
//        return $query;

//        $user = User::with(['organizationUnits.unitable', 'organizationUnits.payments' => function ($q) {
//            $q->where('status_id', 46);
//        }])->find(40);
//        $a = $this->calculatePrice($user);
//        dd($a);
//        try {
//            \DB::beginTransaction();
//            //        $ous = RecruitmentScript::whereHas()
//            $ous = OrganizationUnit::where('unitable_type', DistrictOfc::class)->with('head')->get();
////        dd(implode(',', $ous->pluck('id')->toArray()));
//            $ous->each(function ($ounit) {
//                $user = $ounit->head;
//
//                $organ = $ounit;
//
//                $positionTitle = 'بخشدار';
//                $mrInfo = $this->getMrIdUsingPositionTitle($positionTitle);
//
//                $mrId = $mrInfo['title'];
//                $mr = MR::where('title', $mrId)->first();
//                $meetingTemplate = Meeting::where('isTemplate', true)
//                    ->where('ounit_id', $organ->id)
//                    ->with(['meetingMembers' => function ($query) use ($mr) {
//                        $query->where('mr_id', $mr->id);
//                    }])->first();
//
//
//                if ($meetingTemplate) {
//
//                    $meetingMember = $meetingTemplate->meetingMembers->first();
//                    if (is_null($meetingMember)) {
//                        $mm = new MeetingMember();
//                        $mm->employee_id = $user->id;
//                        $mm->meeting_id = $meetingTemplate->id;
//                        $mm->mr_id = $mr->id;
//                        $mm->save();
//                    } else {
//
//                        $oldUser = User::with(['activeRecruitmentScript' => function ($q) use ($organ,) {
//                            $q->where('organization_unit_id', '=', $organ->id)
//                                ->where('script_type_id', '=', 22)
//                                ->where('position_id', '=', 2);
//                        }])->find($meetingMember->employee_id);
//
//                        $activeRs = $oldUser->activeRecruitmentScript;
////                        if ($activeRs->isNotEmpty()) {
////
////
////                            $statusAzlId = $this->terminatedRsStatus();
////
////                            $this->attachStatusToRs($activeRs->first(), $statusAzlId);
////
////
////                        }
//                        $meetingMember->employee_id = $user->id;
//                        $meetingMember->save();
//                    }
//                } else {
//
//
//                    $data['creatorID'] = $user->id;
//                    $data['meetingTypeID'] = MeetingType::where('title', 'الگو')->first()->id;
//                    $data['isTemplate'] = true;
//                    $data['ounitID'] = $organ->id;
//                    $meeting = $this->storeMeeting($data);
//
//                    MeetingMember::create([
//                        'employee_id' => $user->id,
//                        'meeting_id' => $meeting->id,
//                        'mr_id' => $mr->id,
//                    ]);
//
//                }
//            });
//            \DB::commit();
//        } catch (Exception $e) {
//            \DB::rollBack();
//            dd($e->getMessage());
//        }


//        $recstatus = RecruitmentScriptStatus::create([
//            'recruitment_script_id' => 2803,
//            ''
//        ]);
//        $a = OrganizationUnit::find(1);
//        $cities = $a->children;
//        $startDate = '1403/01/01';
//        $endDate = '1403/12/29';
//
//        $startDate = convertJalaliPersianCharactersToGregorian($startDate);
//
//        $endDate = convertJalaliPersianCharactersToGregorian
//        ($endDate);
//
//        $cities = $cities->load(['cityMeetings' => function ($query) use ($startDate, $endDate) {
//            $query
//                ->where('isTemplate', false)
//                ->whereBetween('meeting_date', [$startDate, $endDate])
//                ->with(['enactments' => function ($q) {
//                    $q->with(['enactmentReviews' => function ($qq) {
//                        $qq->with(['status']);
//                    }, 'title', 'latestHeyaatMeeting', 'status']);
//                }]);
//        }]);
//
//        $childData = $cities->map(function ($child) {
//            $meetingsCount = $child->cityMeetings->count();
//
//            $enactmentsGrouped = $child->cityMeetings->flatMap(function ($meeting) {
//                return $meeting->enactments;
//            })->groupBy('status.name')->map->count();
//
//            $enactmentsGroupedByUpShot = $child->cityMeetings
//                ->flatMap(fn($meeting) => $meeting->enactments) // Collect all enactments
//                ->groupBy(fn(Enactment $enactment) => $enactment->upshot->name) // Group by upshot name
//                ->map(fn($group) => $group->count()); // Count each group
//
//
//            $reviewsGrouped = $child->cityMeetings->flatMap(function ($meeting) {
//                return $meeting->enactments->flatMap(function ($enactment) {
//                    $reviews = $enactment->enactmentReviews;
//
//                    // Check if the enactment has less than 6 reviews
//                    $missingReviews = 6 - $reviews->count();
//
//                    // Add a 'noVote' entry if there are missing reviews
//                    if ($missingReviews > 0) {
//                        $noVote = collect([
//                            (object)[
//                                'status' => (object)['name' => 'در انتظار راب'],
//                                'count' => $missingReviews,
//                            ]
//                        ]);
//                        return $reviews->concat($noVote);
//                    }
//
//                    return $reviews;
//                });
//            })->groupBy('status.name')->map(function ($group) {
//                // Sum up the counts for 'noVote' or other entries
//                return $group->sum(function ($item) {
//                    return $item->count ?? 1; // Default to 1 if 'count' is not defined
//                });
//            });
//
//
//            return [
//                'name' => $child->name,
//                'meetings_count' => $meetingsCount,
//                'enactments_grouped' => $enactmentsGrouped,
//                'reviews_grouped' => $reviewsGrouped,
//                'upshot_report' => $enactmentsGroupedByUpShot
//            ];
//        });
//        dd($childData);


        //        RecruitmentScriptStatus::create([
//            "recruitment_script_id" => 2795,
//            "status_id" => 60
//        ]);

//        EnactmentStatus::create([
//            'enactment_id' => 29,
//            'status_id' => 89,
//            'operator_id' => 2176
//        ]);

//        RecruitmentScriptStatus::create([
//            "recruitment_script_id" => 2834,
//            'status_id' => 43
//        ]);
//        Meeting::create([
//            'isTemplate' => true,
//            'creator_id' => 2172,
//            'meeting_type_id' => 2,
//            'ounit_id' => 3889,
//        ]);
//        $user = User::find(2172);
//        $userRoles = $user->roles->pluck('name')->toArray();
//
//        $enactment = Enactment::find(29);
//        $enactment->load('status');
//        if ($enactment->status->name !== EnactmentStatusEnum::COMPLETED->value) {
//            return response()->json([
//                'message' => 'مصوبه در وضعیت تکمیل شده قرار ندارد'
//            ], 422);
//        }
//        $myPermissions = $this->getComponentsToRender($userRoles, $enactment->status->name);
//
//
//        $componentsToRender = collect([
//            'FormNumThree' => [
//                // MainEnactment logic
//                'reviewStatuses',
//                'latestMeeting.meetingMembers.user.employee.signatureFile',
//                'attachments',
//                'creator',
//                'title',
//                'meeting.ounit.unitable',
//                'meeting.ounit.ancestorsAndSelf',
//
//                // ConsultingReviewCards logic
//                'consultingMembers.enactmentReviews' => function ($query) use ($enactment) {
//                    $query->where('enactment_id', $enactment->id)->with(['status', 'attachment']);
//                },
//
//                // BoardReviewCards logic
//                'boardMembers.enactmentReviews' => function ($query) use ($enactment) {

//                    $query->where('enactment_id', $enactment->id)->with(['status', 'attachment']);
//                },

        // BoardReviewCards logic


//        EnactmentStatus::create([
//            'enactment_id' => 29,
//            'operator_id' => 2086,
//            'status_id' => 88
//        ]);
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
