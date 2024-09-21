<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\RolesEnum;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\EnactmentTitle;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingType;
use Modules\OUnitMS\app\Models\VillageOfc;

class EMSController extends Controller
{

    use MeetingTrait, MeetingMemberTrait;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    public function addBaseInfo()
    {
        $user = Auth::user();
        $titles = EnactmentTitle::all();
        $ounits = $user->activeRecruitmentScripts()
            ->whereHas('ounit', function ($query) {
                $query->where('unitable_type', VillageOfc::class)->with('ancestors');
            })
            ->whereHas('issueTime', function ($query) {
                $query->where('issue_times.title', 'شروع به همکاری');
            })
            ->with('ounit')
            ->get();


        $result = [
            'enactmentTitles' => $titles,
            'ounits' => $ounits->pluck('ounit'),
        ];

        return response()->json($result);


    }

    public function getHeyaatMembers()
    {
        $user = Auth::user();
        $user->load(['activeRecruitmentScript' => function ($q) {
            $q->orderByDesc('recruitment_scripts.create_date')
                ->limit(1)
                ->with('organizationUnit.descendantsAndSelf');
        }]);
        $ounit = $user?->activeRecruitmentScript[0]->organizationUnit;

        $users = User::whereHas('recruitmentScripts', function ($q) use ($ounit) {
            $q->whereIntegerInRaw('organization_unit_id', $ounit->descendantsAndSelf->pluck('id')->toArray());
        })->with('person.avatar')->get(['id', 'person_id']);

        $consultingMembers = Meeting::where('isTemplate', true)->where('ounit_id', $ounit->id)
            ->with(['meetingMembers' => function ($q) use ($ounit) {
                $q->whereHas('roles', function ($query) {
                    $query->where('name', RolesEnum::KARSHENAS_MASHVARATI->value);
                })->with(['person.avatar', 'mr', 'user' => function ($query) {
                    $query->select('id');
                }]);

            }])
            ->first();

        $boardMembers = Meeting::where('isTemplate', true)->where('ounit_id', $ounit->id)
            ->with(['meetingMembers' => function ($q) use ($ounit) {
                $q->whereHas('roles', function ($query) {
                    $query->where('name', RolesEnum::OZV_HEYAAT->value);
                })->with(['person.avatar', 'mr', 'user' => function ($query) {
                    $query->select('id');
                }]);

            }])
            ->first();

        return response()->json([
            'userList' => $users,
            'consultingMembers' => $consultingMembers?->meetingMembers,
            'boardMembers' => $boardMembers?->meetingMembers
        ]);
    }

    public function updateHeyaatMembers(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $data = $request->all();
            $user->load(['activeRecruitmentScript' => function ($q) {
                $q->orderByDesc('recruitment_scripts.create_date')
                    ->limit(1)
                    ->with('organizationUnit');
            }]);
            $ounit = $user?->activeRecruitmentScript[0]->organizationUnit;

            $meeting = Meeting::where('isTemplate', true)
                ->where('ounit_id', $ounit->id)->first();

            if (is_null($meeting)) {
                $data['creatorID'] = $user->id;
                $data['meetingTypeID'] = MeetingType::where('title', 'الگو')->first()->id;
                $data['ounitID'] = $ounit->id;
                $meeting = $this->storeMeeting($data);
            }

            $decode1 = json_decode($data['boardMembers'], true);
            $decode2 = json_decode($data['consultingMembers'], true);
            $mergedData = array_merge($decode1, $decode2);

            $result = $this->bulkUpdateMeetingMembers($mergedData, $meeting);
            DB::commit();
            return response()->json(['message' => 'باموفقیت بروزرسانی شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'باموفقیت بروزرسانی شد', 'error' => $e->getMessage()], 500);

        }


    }
}
