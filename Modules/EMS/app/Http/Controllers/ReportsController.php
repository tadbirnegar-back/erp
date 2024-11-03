<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\EMS\app\Http\Enums\EnactmentReviewEnum;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingType;
use Modules\HRMS\app\Models\RecruitmentScript;

class ReportsController extends Controller
{
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

        $user = Auth::user();
        $employeeId = $user->id;

        $user->load('person.avatar', 'mr', 'activeDistrictRecruitmentScript.ounit.ancestorsAndSelf');


        $meetings = Meeting::whereHas('meetingMembers', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })
            ->where('isTemplate', false)
            ->whereBetween('meeting_date', [$startDate, $endDate])
            ->with(['enactments' => function ($q) use ($employeeId) {
                $q->with(['enactmentReviews' => function ($qq) use ($employeeId) {
                    $qq->where('user_id', $employeeId)
                        ->with(['status']);
                }, 'title', 'latestMeeting', 'status']);
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
            'ounit' => $user->activeDistrictRecruitmentScript->pluck('ounit')->flatten()->toArray()[0] ?? null,

        ];


        return response()->json($result);
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

        $user = Auth::user();
        $user->load('activeDistrictRecruitmentScript.ounit.ancestorsAndSelf');

        /**
         * @var RecruitmentScript $rs
         */
        $rs = $user->activeDistrictRecruitmentScript->first();

        if (!$rs) {
            return response()->json(['message' => 'شما حکم فعالی مرتبط به بخشداری ندارید'], 404);
        }

        $meetingType = MeetingType::where('title', 'جلسه هیئت تطبیق')->first();

        $meetings = Meeting::where('ounit_id', '=', $rs->ounit->id)
            ->whereBelongsTo($meetingType, 'meetingType')
            ->where('isTemplate', false)
            ->whereBetween('meeting_date', [$startDate, $endDate])
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
                        'mr' => $member->mr,
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
            'members' => $collection->values(),
            'ounit' => $rs->ounit->ancestorsAndSelf,
            'expired_count' => $totalAdamMoghayertAutomatic,
            'approved_count' => $totalAdamMoghayert + $totalMoghayert,
            'enactments' => $meetings->pluck('enactments'),
        ];

        return response()->json($response);
    }

}
