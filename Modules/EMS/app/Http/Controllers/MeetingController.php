<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Requests\UpdateMeetingDateReq;
use Modules\EMS\app\Http\Traits\DateTrait;
use Modules\EMS\app\Http\Traits\EMSSettingTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\EnactmentMeeting;
use Modules\EMS\app\Models\Meeting;

class MeetingController extends Controller
{
    use DateTrait, MeetingTrait, EMSSettingTrait;

    public function changeMeetingDate(UpdateMeetingDateReq $req, $id)
    {
        $data = $req->validated();


        try {
            \DB::beginTransaction();

            $user = Auth::user();

            $rc = $user->load('activeDistrictRecruitmentScript.ounit');


            if (empty($rc->activeDistrictRecruitmentScript[0])) {
                return response()->json([
                    'message' => 'اطلاعاتی در این مورد وجود ندارد'
                ], 404);
            }

            $data['ounitID'] = $rc->activeDistrictRecruitmentScript[0]->ounit->id;
            $data["creatorID"] = $rc->id;
            if (!isset($data['meetingID'])) {
                $meeting = $this->storeMeeting($data);
            } else {
                if (!isset($req->meetingID)) {
                    return response()->json([
                        'Error' => "didn't received 'meetingID'"
                    ], 400);
                }
                $meeting = Meeting::find($data["meetingID"]);
            }

            $enactment = Enactment::with('latestMeeting')->find($id);

            //return response()->json($enactment);
            $meetingMembers = $this->ReplicateDatas($enactment->latestMeeting, $meeting, $rc->activeDistrictRecruitmentScript[0]->ounit);


            EnactmentMeeting::where('meeting_id', $enactment->latestMeeting->id)
                ->where('enactment_id', $id)
                ->get()
                ->each(function ($enactmentMeeting) {
                    $enactmentMeeting->delete(); // Will trigger the observer's deleted method
                });

            EnactmentMeeting::create([
                'meeting_id' => $meeting->id,
                'enactment_id' => $id,
            ]);


            $newDate = $this->DateformatToHumanReadbleJalali($enactment->latestMeeting->meeting_date);
            $lastDate = $this->DateformatToHumanReadbleJalali($meeting->meeting_date);


            $users = $meetingMembers->pluck('employee_id')->toArray();

            // Fetch users with their related person data
            $fetchedUsers = User::whereIn('id', $users)->with('person')->get();

            foreach ($fetchedUsers as $user) {
                $username = $user->person->display_name;
                //$user->notify(new ChangeMeetingDateNotification($username, $lastDate, $newDate));
            }
            \DB::commit();

            return response()->json([
                'dateTime' => $meeting->meeting_date,
            ], 200);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                "error" => "تغییر تاریخ جلسه انجام نشد!",
            ], 500);
        }
    }


    public function getSelection()
    {
//        $user = User::find(2086);
        $user = Auth::user();

        $user->load('activeDistrictRecruitmentScript.organizationUnit.firstFreeMeetingByNow');
        $user->load('activeDistrictRecruitmentScript.organizationUnit.fullMeetingsByNow');

        $firstFreeMeeting = $user->activeDistrictRecruitmentScript->first()?->organizationUnit->firstFreeMeetingByNow;
        $fullMeetings = $user->activeDistrictRecruitmentScript->first()?->organizationUnit->fullMeetingsByNow;


        $data = [];
        if (!empty($fullMeetings)) {
            $data['fullMeetings'] = $fullMeetings;
        }

        if (empty($firstFreeMeeting)) {
            $data['message'] = "هیچ جلسه ای خالی نیست";
        } else {

            $enactmentLimitPerMeeting = $this->getEnactmentLimitPerMeeting();

            $EncInMeetingcount = EnactmentMeeting::where('meeting_id', $firstFreeMeeting->id)
                ->distinct('enactment_id')
                ->count('enactment_id');

            if ($enactmentLimitPerMeeting->value <= $EncInMeetingcount) {
                $data['message'] = "جلسه انتخاب شده تکمیل ظرفیت شده";
            }

            $data['freeMeeting'] = $firstFreeMeeting;

        }


        return response()->json($data, 200);
    }
}
