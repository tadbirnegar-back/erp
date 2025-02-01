<?php

namespace App\Http\Controllers;


use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\Gateway\app\Http\Traits\PaymentRepository;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\LMS\app\Http\Traits\ExamsTrait;
use Modules\LMS\app\Models\Exam;
use Modules\OUnitMS\app\Models\VillageOfc;


class testController extends Controller
{
    use PaymentRepository, ApprovingListTrait, EnactmentTrait, MeetingMemberTrait, RecruitmentScriptTrait, MeetingTrait;
    use ExamsTrait;

    public function run()
    {
$a=["0415010001043080", "0415010001043081", "0415010001043103", "0415010001043087", "0415010001342754", "0415010001804190", "0415010001043091", "0415010001043092", "0415010001043093", "0415010001043096", "0415010001043102", "0415010001043105", "0415010001043082", "0415010002043083", "0415010002043101", "0415010002804189", "0415010002043108", "0415020001042820", "0415020001042828", "0415020001341193", "0415020001341195", "0415020001042831", "0415020001042835", "0415020001042849", "0415020001042855", "0415020001042857", "0415020001042858", "0415020001042856", "0415020001042859", "0415020001042861", "0415020001042886", "0415020001042885", "0415020001042889", "0415020002042819", "0415020002042823", "0415020002804202", "0415020002042821", "0415020002342573", "0415020002042827", "0415020002904478", "0415020002341361", "0415020002341362", "0415020002904479", "0415020002042832", "0415020002042833", "0415020002042834", "0415020002042837", "0415020002904477", "0415020002042840", "0415020002042841", "0415020002804205", "0415020002804204", "0415020002904475", "0415020002804203", "0415020002904476", "0415020002042847", "0415020002042854", "0415020002042864", "0415020002042866", "0415020002042868", "0415020002042869", "0415020002042871", "0415020002042872", "0415020002042876", "0415020002042877", "0415020002042880", "0415020002042881", "0415020002904474", "0415020002904473", "0415020002904480", "0415020002042888", "0415020002042890", "0415020002042894", "0415020002042892", "0415020002042895", "0415020002904472", "0417010001042991", "0417010001043005", "0417010001043007", "0417010001043016", "0417010001043019", "0417010001043020", "0417010002042988", "0417010002042989", "0417010002042992", "0417010002042999", "0417010002043097", "0417010002043104", "0417010002043029", "0417020001042982", "0417020001042984", "0417020001042990", "0417020001042830", "0417020001904456", "0417020001042838", "0417020001042993", "0417020001042997", "0417020001904458", "0417020001043002", "0417020001043006", "0417020001043010", "0417020001043011", "0417020001043012", "0417020001043013", "0417020001904455", "0417020001043017", "0417020001043018", "0417020001904459", "0417020001042884", "0417020001904457", "0417020002042887", "0417020002043606", "0417020002042829", "0417020002804161", "0417020002042836", "0417020002042882", "0417020002042843", "0417020002042844", "0417020002042845", "0417020002042846", "0417020002043627", "0417020002904461", "0417020002042863", "0417020002042862", "0417020002042867", "0417020002042870", "0417020002043607", "0417020002042878", "0417020002042879", "0417020002042883", "0417020002904460", "0417020002042824", "0417020002042891", "0417020002042896", "0417020002042897", "0417020002042899", "0406050001341165", "0406050001341169", "0406050001341161", "0406050001341163", "0406050001341160", "0406050001341170", "0406050001341167", "0406050001043608", "0406050001043610", "0406050001043612", "0406050001341168", "0406050001043618", "0406050001043625", "0406050001043630", "0406050001804228", "0406050001342528", "0406050001043637", "0406050001043638", "0406050001043631", "0406050001043617", "0406050001043634", "0406050001342530", "0406050001043647", "0406050001804224", "0406050001043650", "0406050001043651", "0406050001804225", "0406050001043652", "0406050001342513", "0406050001043653", "0406050001043654", "0406050001043658", "0406050001043660", "0406050001043663", "0406050001043666", "0406050002904432", "0406050002904434", "0406050002904435", "0406050002341276", "0406050002341307", "0406050002043737", "0406050002043738", "0406050002043742", "0406050002043746", "0406050002043747", "0406050002043648", "0406050002904443", "0406050002804222", "0406050002043754", "0406050002904446", "0406050002043635", "0406050002043640", "0406050002043759", "0406050002904447", "0406050002043765", "0406050002904448", "0406050002904449", "0406050002341280", "0406050002043775", "0406050002342605", "0406050002043777", "0406050002043778", "0406050002043779", "0406040001042818", "0406040001043603", "0406040001043604", "0406040001043605", "0406040001042825", "0406040001042826", "0406040001043609", "0406040001043611", "0406040001342755", "0406040001043740", "0406040001043613", "0406040001043614", "0406040001043615", "0406040001043616", "0406040001042839", "0406040001043619", "0406040001043620", "0406040001043621", "0406040001042842", "0406040001043622", "0406040001043626", "0406040001043628", "0406040001804209", "0406040001043755", "0406040001042860", "0406040001043636", "0406040001043639", "0406040001043643", "0406040001043644", "0406040001043645", "0406040001043760", "0406040001043766", "0406040001043767", "0406040001043771", "0406040001043656", "0406040001043657", "0406040001804210", "0406040001043661", "0406040001042893", "0406040001043662", "0406040001043664", "0406040001043665", "0406040001042898", "0406040005042822", "0406040005043646", "0406040005341150", "0406040005341157", "0406040005341151", "0406040005342514", "0406040005042853", "0406040005043623", "0406040005043624", "0406040005042848", "0406040005042850", "0406040005042851", "0406040005042852", "0406040005043629", "0406040005043633", "0406040005042865", "0406040005043641", "0406040005043667", "0406040005042873", "0406040005042874", "0406040005042875", "0406040005043642", "0406040005043649", "0406040005043659", "0406040003804213", "0406040003043731", "0406040003904436", "0406040003043367", "0406040003904438", "0406040003043734", "0406040003043732", "0406040003043735", "0406040003043371", "0406040003342603", "0406040003043736", "0406040003904439", "0406040003043739", "0406040003804212", "0406040003043741", "0406040003043744", "0406040003904440", "0406040003043745", "0406040003043748", "0406040003043749", "0406040003804223", "0406040003043750", "0406040003043396", "0406040003904441", "0406040003804218", "0406040003043751", "0406040003043752", "0406040003043753", "0406040003904445", "0406040003804214", "0406040003043430", "0406040003043756", "0406040003043757", "0406040003043758", "0406040003043761", "0406040003043763", "0406040003043764", "0406040003043769", "0406040003043770", "0406040003043772", "0406040003043768", "0406040003043773", "0406040003904450", "0406040003341311", "0406040003804211", "0406040003043774", "0406040003043776", "0406040003904451"];

\DB::beginTransaction();

        foreach ($a as $item) {
            $vill = VillageOfc::where('abadi_code', $item)->first();
            $vill->free_zone_id = 1;
            $vill->save();
}
\DB::commit();
        dd('done');


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

        $query = Exam::joinRelationship('course');
        $query->leftJoinRelationship('questions');
        $query->addSelect([
            'exams.title as examTitle',
            'courses.title as coursesTitle',
            'questions.title as question_title',
        ]);
        $query->withCount(['questions as totalQuestions']);
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

        return $query->where('exams.id', 1)->get();
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
