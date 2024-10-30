<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\EMS\app\Models\Meeting;

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
                }, 'title', 'meeting', 'status']);
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

}
