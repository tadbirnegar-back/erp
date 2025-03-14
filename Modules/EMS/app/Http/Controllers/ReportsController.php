<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\EnactmentReviewEnum;
use Modules\EMS\app\Http\Enums\EnactmentStatusEnum;
use Modules\EMS\app\Http\Enums\MeetingTypeEnum;
use Modules\EMS\app\Http\Traits\ReportingTrait;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingType;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

class ReportsController extends Controller
{
    use ReportingTrait;

    /**
     * Display a listing of the resource.
     */
    public function myEnactmentsReport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'startDate' => 'required',
            'endDate' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $startDate = convertJalaliPersianCharactersToGregorian($request->input('startDate'));

        $endDate = convertJalaliPersianCharactersToGregorian
        ($request->input('endDate'));

//        return response()->json([$startDate, $endDate]);

        if (isset($request->employeeID)) {
            $user = User::find($request->employeeID);
        } else {
            $user = Auth::user();
        }
        $employeeId = $user->id;

        $user->load('activeDistrictRecruitmentScript.ounit.ancestorsAndSelf');

        $rsUnits = $user->activeDistrictRecruitmentScript->pluck('ounit')->unique()->flatten();

        if ($request->ounitID) {
            $ounit = OrganizationUnit::with('ancestorsAndSelf')->find($request->ounitID);
        } else {
            $ounit = $rsUnits[0];
        }

        $user->load('person.avatar', 'mr');

        $meetingType = MeetingType::where('title', MeetingTypeEnum::HEYAAT_MEETING->value)->first();

        $meetings = Meeting::whereHas('meetingMembers', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })
            ->where('ounit_id', $ounit->id)
            ->where('isTemplate', false)
            ->whereBelongsTo($meetingType, 'meetingType')
            ->whereBetween('meeting_date', [$startDate, $endDate])
            ->with(['enactments' => function ($q) use ($employeeId) {
                $q
                    ->whereDoesntHave('status', function ($query) {
                        $query->where('statuses.name', EnactmentStatusEnum::CANCELED->value);
                    })
                    ->with(['enactmentReviews' => function ($qq) use ($employeeId) {
                        $qq->where('user_id', $employeeId)
                            ->with(['status']);
                    }, 'title', 'latestHeyaatMeeting', 'status', 'ounit.ancestorsAndSelf' => function ($q) {
                        $q->where('unitable_type', '!=', StateOfc::class);
                    }]);
            }])
            ->with(['meetingMembers' => function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId)->with('mr');


            }, 'ounit.ancestorsAndSelf'])
            ->get();

        $meetings->each(function ($meeting) {
            // Collect all review statuses across enactments within the meeting and count each dynamically
            $totalReviewCounts = $meeting->enactments
                ->flatMap(function ($enactment) {
                    return $enactment->enactmentReviews;
                })
                ->groupBy('status.name')//                ->map->count()
            ;

            // Attach total counts to the meeting for easy access
            $meeting->total_review_counts = $totalReviewCounts;
        });

        $result = [
            'meeting_count' => $meetings->count(),
            'enactments_count' => $meetings->pluck('enactments')->flatten()->count(),
            'total_review_counts' => $meetings->pluck('total_review_counts')
//                ->flatten()->sum()
            ,
            'enactments' => $meetings->pluck('enactments'),
            'person' => $user->person,
            'mr' => $user?->mr,
//            'ounit' => $meetings->pluck('ounit')->flatten()->toArray()[0] ?? null,
            'ounit' => $ounit ?? null,

        ];


        return response()->json(['data' => $result, 'ounits' => $rsUnits]);
    }


    public function myEnactmentsReportFreeZone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'startDate' => 'required',
            'endDate' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $startDate = convertJalaliPersianCharactersToGregorian($request->input('startDate'));

        $endDate = convertJalaliPersianCharactersToGregorian
        ($request->input('endDate'));

//        return response()->json([$startDate, $endDate]);

        if (isset($request->employeeID)) {
            $user = User::find($request->employeeID);
        } else {
            $user = Auth::user();
        }
        $employeeId = $user->id;

        $user->load('activeFreeZoneRecruitmentScript.ounit.ancestorsAndSelf');

        $ounits = $this->findDistrictsByFreeZone($user->activeFreeZoneRecruitmentScript);

        $rsUnits = $user->activeFreeZoneRecruitmentScript->pluck('ounit')->unique()->flatten();

        $user->load('person.avatar', 'mr');

        $meetingType = MeetingType::where('title', MeetingTypeEnum::FREE_ZONE->value)->first();

        $meetings = Meeting::whereHas('meetingMembers', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })
            ->whereIn('ounit_id', $ounits)
            ->where('isTemplate', false)
            ->whereBelongsTo($meetingType, 'meetingType')
            ->whereBetween('meeting_date', [$startDate, $endDate])
            ->with(['enactments' => function ($q) use ($employeeId) {
                $q
                    ->whereDoesntHave('status', function ($query) {
                        $query->where('statuses.name', EnactmentStatusEnum::CANCELED->value);
                    })
                    ->with(['enactmentReviews' => function ($qq) use ($employeeId) {
                        $qq->where('user_id', $employeeId)
                            ->with(['status']);
                    }, 'title', 'latestHeyaatMeeting', 'status', 'ounit.ancestorsAndSelf' => function ($q) {
                        $q->where('unitable_type', '!=', StateOfc::class);
                    }]);
            }])
            ->with(['meetingMembers' => function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId)->with('mr');


            }, 'ounit.ancestorsAndSelf'])
            ->get();

        $meetings->each(function ($meeting) {
            // Collect all review statuses across enactments within the meeting and count each dynamically
            $totalReviewCounts = $meeting->enactments
                ->flatMap(function ($enactment) {
                    return $enactment->enactmentReviews;
                })
                ->groupBy('status.name')//                ->map->count()
            ;

            // Attach total counts to the meeting for easy access
            $meeting->total_review_counts = $totalReviewCounts;
        });

        $result = [
            'meeting_count' => $meetings->count(),
            'enactments_count' => $meetings->pluck('enactments')->flatten()->count(),
            'total_review_counts' => $meetings->pluck('total_review_counts')
//                ->flatten()->sum()
            ,
            'enactments' => $meetings->pluck('enactments'),
            'person' => $user->person,
            'mr' => $user?->mr,
//            'ounit' => $meetings->pluck('ounit')->flatten()->toArray()[0] ?? null,
            'ounit' => $ounit ?? null,

        ];


        return response()->json(['data' => $result, 'ounits' => $rsUnits]);
    }

    public function districtEnactmentReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'startDate' => 'required',
            'endDate' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $startDate = convertJalaliPersianCharactersToGregorian($request->input('startDate'));

        $endDate = convertJalaliPersianCharactersToGregorian
        ($request->input('endDate'));

        if ($request->ounitID) {
            $ounit = OrganizationUnit::with('ancestorsAndSelf')->find($request->ounitID);
        } else {
            $user = Auth::user();
            $user->load('activeDistrictRecruitmentScript.ounit.ancestorsAndSelf');

            /**
             * @var RecruitmentScript $rs
             */
            $rs = $user->activeDistrictRecruitmentScript->first();

            if (!$rs) {
                return response()->json(['message' => 'شما حکم فعالی مرتبط به بخشداری ندارید'], 404);
            }

            $ounit = $rs->ounit;
        }

        $meetingType = MeetingType::where('title', MeetingTypeEnum::HEYAAT_MEETING->value)->first();

        $meetings = Meeting::where('ounit_id', '=', $ounit->id)
            ->whereBelongsTo($meetingType, 'meetingType')
            ->where('isTemplate', false)
            ->whereBetween('meeting_date', [$startDate, $endDate])
            ->with(['enactments' => function ($q) {
                $q
                    ->whereDoesntHave('status', function ($query) {
                        $query->where('statuses.name', EnactmentStatusEnum::CANCELED->value);
                    })
                    ->with(['enactmentReviews' => function ($qq) {
                        $qq->with(['status']);
                    }, 'title', 'latestHeyaatMeeting', 'status']);
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
                        'mr' => $member->mr,
                        'employee_id' => $employeeId,
                    ];
                }

                $membersResult[$employeeId]['meeting_count'] = ($membersResult[$employeeId]['meeting_count'] ?? 0) + 1;

                foreach ($meeting->enactments as &$enactment) {
                    // Find the review for this employee in the current enactment
                    $review = $enactment->enactmentReviews->firstWhere('user_id', $employeeId);

                    $membersResult[$employeeId]['enactment_count'] = ($membersResult[$employeeId]['enactment_count'] ?? 0) + 1;

                    if ($review) {
                        $review->setAttribute('person', $member->person);
                        // Increment the count for the review's status
                        $status = $review->status->name;
                        $membersResult[$employeeId][$status] = ($membersResult[$employeeId][$status] ?? 0) + 1;
                    } else {
                        // If no review is found, increment 'در انتظار بررسی'
                        $membersResult[$employeeId][EnactmentReviewEnum::PENDING->value] = ($membersResult[$employeeId][EnactmentReviewEnum::PENDING->value] ?? 0) + 1;
                    }
                    $enactment->setAttribute('members', $meeting->meetingMembers);
                }
            }
        }
        $collection = collect($membersResult);

// Calculate sums
        $totalMoghayert = $collection->sum(EnactmentReviewEnum::INCONSISTENCY->value);
        $totalAdamMoghayert = $collection->sum(EnactmentReviewEnum::NO_INCONSISTENCY->value);
        $totalAdamMoghayertAutomatic = $collection->sum(EnactmentReviewEnum::SYSTEM_NO_INCONSISTENCY->value);
        $totalPending = $collection->sum(EnactmentReviewEnum::PENDING->value);

        $response = [
            'totalMeetingCount' => $meetings->count(),
            'totalEnactmentCount' => $meetings->pluck('enactments')->flatten()->count(),
            'totalMoghayert' => $totalMoghayert,
            'totalAdamMoghayert' => $totalAdamMoghayert,
            'totalAdamMoghayertAutomatic' => $totalAdamMoghayertAutomatic,
            'totalPending' => $totalPending,
            'members' => $collection->isNotEmpty() ? $collection->values() : $ounit->meetingMembers,
            'ounit' => $ounit->ancestorsAndSelf,
            'expired_count' => $totalAdamMoghayertAutomatic,
            'approved_count' => $totalAdamMoghayert + $totalMoghayert,
            'enactments' => $meetings->pluck('enactments'),
        ];

        return response()->json($response);
    }

    public function cityEnactmentReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ounitID' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $startDate = convertJalaliPersianCharactersToGregorian($request->input('startDate'));

        $endDate = convertJalaliPersianCharactersToGregorian
        ($request->input('endDate'));

//        $user = Auth::user();

        try {
//            $user->load('activeCityRecruitmentScript.ounit');
//
//            /**
//             * @var RecruitmentScript $rs
//             */
//            $rs = $user->activeCityRecruitmentScript->first();
//
//            if (!$rs) {
//                return response()->json(['message' => 'شما حکم فعالی مرتبط به فرمانداری ندارید'], 404);
//            }

            $childOunits = OrganizationUnit::with(['children.ancestors' => function ($query) {
                $query->where('unitable_type', '!=', StateOfc::class);

            }])->find($request->ounitID);

            $meetingType = MeetingType::where('title', 'جلسه هیئت تطبیق')->first();

            $ounit = $childOunits->load(['children.meetings' => function ($query) use ($meetingType, $startDate, $endDate) {
                $query->whereBelongsTo($meetingType, 'meetingType')
                    ->where('isTemplate', false)
                    ->whereBetween('meeting_date', [$startDate, $endDate])
                    ->with(['enactments' => function ($q) {
                        $q
                            ->whereDoesntHave('status', function ($query) {
                                $query->where('statuses.name', EnactmentStatusEnum::CANCELED->value);
                            })
                            ->with(['enactmentReviews' => function ($qq) {
                                $qq->with(['status']);
                            }, 'title', 'latestHeyaatMeeting', 'status']);
                    }]);
            }]);


            $childData = $ounit->children->map(function ($child) {
                $meetingsCount = $child->meetings->count();

                $enactmentsGrouped = $child->meetings->flatMap(function ($meeting) {
                    return $meeting->enactments;
                })->groupBy('status.name')->map->count();

                $enactmentsGroupedByUpShot = $child->meetings
                    ->flatMap(fn($meeting) => $meeting->enactments) // Collect all enactments
                    ->groupBy(fn(Enactment $enactment) => $enactment->upshot->name) // Group by upshot name
                    ->map(fn($group) => $group->count()); // Count each group


                $reviewsGrouped = $child->meetings->flatMap(function ($meeting) {
                    return $meeting->enactments->flatMap(function ($enactment) {
                        $reviews = $enactment->enactmentReviews;

                        // Check if the enactment has less than 6 reviews
                        $missingReviews = 6 - $reviews->count();

                        // Add a 'noVote' entry if there are missing reviews
                        if ($missingReviews > 0) {
                            $noVote = collect([
                                (object)[
                                    'status' => (object)['name' => 'نامشخص'],
                                    'count' => $missingReviews,
                                ]
                            ]);
                            return $reviews->concat($noVote);
                        }

                        return $reviews;
                    });
                })->groupBy('status.name')->map(function ($group) {
                    // Sum up the counts for 'noVote' or other entries
                    return $group->sum(function ($item) {
                        return $item->count ?? 1; // Default to 1 if 'count' is not defined
                    });
                });


                return [
                    'name' => $child->name,
                    'ounit_id' => $child->id,
                    'ancestors' => $child->ancestors,
                    'meetings_count' => $meetingsCount,
                    'enactments_grouped' => $enactmentsGrouped,
                    'reviews_grouped' => $reviewsGrouped,
                    'upshot_report' => $enactmentsGroupedByUpShot
                ];
            });

            return response()->json($childData);
        } catch (\Exception $e) {
            return response()->json(['message' => 'error'], 500);
        }

    }

    public function stateEnactmentReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'startDate' => 'required',
            'endDate' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $startDate = convertJalaliPersianCharactersToGregorian($request->input('startDate'));

        $endDate = convertJalaliPersianCharactersToGregorian
        ($request->input('endDate'));

        $user = Auth::user();

        try {
            $user->load('activeStateRecruitmentScript.ounit.children');

            /**
             * @var RecruitmentScript $rs
             */
            $rs = $user->activeStateRecruitmentScript->first();

            if (!$rs) {
                return response()->json(['message' => 'شما حکم فعالی مرتبط به استانداری ندارید'], 404);
            }
            $cities = $rs->ounit->children;

            $cities = $cities->load(['cityMeetings' => function ($query) use ($startDate, $endDate) {
                $query
                    ->where('isTemplate', false)
                    ->whereBetween('meeting_date', [$startDate, $endDate])
                    ->with(['enactments' => function ($q) {
                        $q
                            ->whereDoesntHave('status', function ($query) {
                                $query->where('statuses.name', EnactmentStatusEnum::CANCELED->value);
                            })
                            ->with(['enactmentReviews' => function ($qq) {
                                $qq->with(['status']);
                            }, 'title', 'latestHeyaatMeeting', 'status']);
                    }]);
            }]);


            $childData = $cities->map(function ($child) {
                $meetingsCount = $child->cityMeetings->count();

                $enactmentsGrouped = $child->cityMeetings->flatMap(function ($meeting) {
                    return $meeting->enactments;
                })->groupBy('status.name')->map->count();

                $enactmentsGroupedByUpShot = $child->cityMeetings
                    ->flatMap(fn($meeting) => $meeting->enactments) // Collect all enactments
                    ->groupBy(fn(Enactment $enactment) => $enactment->upshot->name) // Group by upshot name
                    ->map(fn($group) => $group->count()); // Count each group


                $reviewsGrouped = $child->cityMeetings->flatMap(function ($meeting) {
                    return $meeting->enactments->flatMap(function ($enactment) {
                        $reviews = $enactment->enactmentReviews;

                        // Check if the enactment has less than 6 reviews
                        $missingReviews = 6 - $reviews->count();

                        // Add a 'noVote' entry if there are missing reviews
                        if ($missingReviews > 0) {
                            $noVote = collect([
                                (object)[
                                    'status' => (object)['name' => 'نامشخص'],
                                    'count' => $missingReviews,
                                ]
                            ]);
                            return $reviews->concat($noVote);
                        }

                        return $reviews;
                    });
                })->groupBy('status.name')->map(function ($group) {
                    // Sum up the counts for 'noVote' or other entries
                    return $group->sum(function ($item) {
                        return $item->count ?? 1; // Default to 1 if 'count' is not defined
                    });
                });


                return [
                    'name' => $child->name,
                    'ounit_id' => $child->id,
                    'meetings_count' => $meetingsCount,
                    'enactments_grouped' => $enactmentsGrouped,
                    'reviews_grouped' => $reviewsGrouped,
                    'upshot_report' => $enactmentsGroupedByUpShot
                ];
            });


            return response()->json($childData);


        } catch (Exception $e) {
            return response()->json(['message' => 'error'], 500);
        }
    }
}
