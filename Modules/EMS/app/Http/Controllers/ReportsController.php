<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Requests\BakhshdariEnactmentReq;
use Modules\EMS\app\Models\Meeting;
use Modules\OUnitMS\app\Models\OrganizationUnit;

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


    public function bakhshdarEnactmentsReport(BakhshdariEnactmentReq $req)
    {
        //Make dates better
        $startDate = convertJalaliPersianCharactersToGregorian($req->input('startDate'));
        $endDate = convertJalaliPersianCharactersToGregorian($req->input('endDate'));

        // Retrieve user's position and recruitment scripts
        $rcs = User::with('activeDistrictRecruitmentScript')->find(2060);

        // Check if active_district_recruitment_script is not null
        if (!$rcs || !$rcs->activeDistrictRecruitmentScript) {
            return response()->json(['message' => 'No active district recruitment script found for the user.'], 404);
        }


        // Extract organization_unit_id from recruitment script
        $organizationUnitIds = $rcs->activeDistrictRecruitmentScript->pluck('organization_unit_id');

        //Get Name of state or city
        $ounit = OrganizationUnit::find($organizationUnitIds[0]);

        $units = OrganizationUnit::where("unitable_id", $ounit->unitable_id)
            ->whereIn("unitable_type", [
                "Modules\\OUnitMS\\app\\Models\\StateOfc",
                "Modules\\OUnitMS\\app\\Models\\CityOfc"
            ])
            ->get();

        $cityAndStateNames = [
            'states' => $units->where('unitable_type', 'Modules\\OUnitMS\\app\\Models\\StateOfc')->pluck('name'),
            'cities' => $units->where('unitable_type', 'Modules\\OUnitMS\\app\\Models\\CityOfc')->pluck('name'),
        ];

        $organName = $ounit->unitable->name;

        // Query meetings based on organization_unit_id using the ounit relationship
        $meetings = Meeting::whereHas('ounit', function ($query) use ($organizationUnitIds) {
            $query->whereIn('id', $organizationUnitIds);
        })
            ->whereBetween('meeting_date', [$startDate, $endDate])
            ->with(['enactments.enactmentReviews.user.person']) // Eager load users and their person info
            ->get();

// Initialize separate arrays for meetings and enactments
        $meetingData = [];
        $enactmentData = [];
        $totalReviewCount = 0; // Initialize total review count

// Initialize status counters
        $statusCounts = [
            'مغایرت' => 0,
            'عدم مغایرت' => 0,
            'در حال بررسی' => 0,
        ];

// Process the meetings and separate enactments
        foreach ($meetings as $meeting) {
            $meetingData[] = [
                'id' => $meeting->id,
                'title' => $meeting->title,
                'meeting_number' => $meeting->meeting_number,
                'summary' => $meeting->summary,
                'creator_id' => $meeting->creator_id,
                'meeting_type_id' => $meeting->meeting_type_id,
                'ounit_id' => $meeting->ounit_id,
                'meeting_date' => $meeting->meeting_date,
            ];

            // Separate enactments
            foreach ($meeting->enactments as $enactment) {
                // Collect enactment reviews
                $enactmentReviews = $enactment->enactmentReviews->map(function ($review) use (&$statusCounts) {
                    // Count statuses
                    $statusName = $review->status->name;

                    // Increment the respective status counter
                    if ($statusName === 'مغایرت') {
                        $statusCounts['مغایرت']++;
                    } elseif ($statusName === 'عدم مغایرت') {
                        $statusCounts['عدم مغایرت']++;
                    } else {
                        $statusCounts['در حال بررسی']++;
                    }

                    // Return review data including user information and profile_picture_id
                    return [
                        'id' => $review->id,
                        'description' => $review->description,
                        'attachment_id' => $review->attachment_id,
                        'enactment_id' => $review->enactment_id,
                        'status_id' => $review->status_id,
                        'create_date' => $review->create_date,
                        'status' => [
                            'id' => $review->status->id,
                            'name' => $statusName,
                        ],
                        'user' => [ // Include user details along with profile_picture_id
                            'id' => $review->user->id,
                            'name' => $review->user->name,
                            'person_id' => $review->user->person_id,
                            'profile_picture_id' => $review->user->person->profile_picture_id, // Fetch profile_picture_id from Person
                            // Add other user fields as needed
                        ],
                    ];
                });

                // Increment the total review count by the number of reviews for this enactment
                $totalReviewCount += $enactmentReviews->count();

                $enactmentData[] = [
                    'id' => $enactment->id,
                    'custom_title' => $enactment->custom_title,
                    'title_id' => $enactment->title_id,
                    'creator_id' => $enactment->creator_id,
                    'upshot' => $enactment->upshot,
                    'pivot' => $enactment->pivot,
                    'title' => $enactment->title,
                    'meeting_id' => $meeting->id,
                    'reviews' => $enactmentReviews,
                ];
            }
        }

// Return the filtered meetings as JSON
        return response()->json([
            "meeting_count" => $meetings->count(),
            "Orginazations" => $cityAndStateNames,
            "meetings" => $meetingData,
            "enactments_count" => count($enactmentData),
            "total_review_count" => $totalReviewCount,
            "status_counts" => $statusCounts,
            "enactments" => $enactmentData,
        ]);
    }
}
