<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Requests\UpdateMeetingDateReq;
use Modules\EMS\app\Http\Traits\DateTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\EnactmentMeeting;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Notifications\ChangeMeetingDateNotification;
use Modules\PersonMS\app\Models\Person;

class MeetingController extends Controller
{
    use DateTrait, MeetingTrait;

    public function changeMeetingDate(UpdateMeetingDateReq $req, $id)
    {
        $data = $req->validated();


        try {

            \DB::beginTransaction();
            $userOrgan = User::with('activeRecruitmentScript.ounit')->find(\Auth::user()->id);

            $data['ounitID'] = $userOrgan->activeRecruitmentScript[0]->ounit->id;
            $data["creatorID"] = $userOrgan->id;
            if (!isset($data['meetingId'])) {
                $meeting = $this->storeMeeting($data);
            } else {
                if (!isset($req->meetingId)) {
                    return response()->json([
                        'Error' => "didn't received 'meetingId'"
                    ], 400);
                }
                $meeting = Meeting::find($data["meetingId"]);
            }

            $enactment = Enactment::with('latestMeeting')->find($id);
            $meetingMembers = $this->ReplicateDatas($enactment->latestMeeting, $meeting, $userOrgan->activeRecruitmentScript[0]->ounit);
            EnactmentMeeting::create([
                'meeting_id' => $meeting->id,
                'enactment_id' => $id,
            ]);


            /*New Date Start*/
            $parts = explode('/', $enactment->latestMeeting->meeting_date); // Split the date string by '/'
            $monthNumber = $parts[1]; // Get the second part as the month number
            $day = $parts[2];
            //For Month
            $eng = $this->persianNumbersToEng($monthNumber);
            $monthName = $this->humanReadableDate($eng);
            //For Day
            $daywithoutZero = $this->removeLeftZero($monthNumber);
            //message text for date
            $messageTextDate = "$daywithoutZero $monthName $parts[0]";
            /*New Date End*/

            /*Start Last Date*/
            $parts = explode('/', $meeting->meeting_date); // Split the date string by '/'
            $monthNumber = $parts[1]; // Get the second part as the month number
            $day = $parts[2];
            //For Month
            $eng = $this->persianNumbersToEng($monthNumber);
            $monthName = $this->humanReadableDate($eng);
            //For Day
            $daywithoutZero = $this->removeLeftZero($monthNumber);
            //message text for date
            $messageTextLastDate = "$daywithoutZero $monthName $parts[0]";


            foreach ($meetingMembers as $member) {
                $user = User::find($member->employee_id);
                $username = Person::find($user->person_id)->display_name;

                $user->notify(new ChangeMeetingDateNotification($username, $messageTextLastDate, $messageTextDate));
            }
            \DB::commit();

            return response()->json([
                'dateTime' => $meeting->meeting_date,
            ], 200);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                "error" => "Invalid date format or conversion failed.",
            ], 500);
        }
    }
}
