<?php

namespace Modules\EMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\EMS\app\Http\Enums\MeetingStatusEnum;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingStatus;
use Modules\OUnitMS\app\Models\OrganizationUnit;

trait MeetingTrait
{

    public function storeMeeting(array|Collection $data)
    {

//        $normalizedDate = str_replace('-', '/', $data['newDate']);
//        $englishJalaliDateString = \Morilog\Jalali\CalendarUtils::convertNumbers($normalizedDate, true);
//        $dateTimeString = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d', $englishJalaliDateString)
//            ->toDateTimeString();
//        $data["meetingDate"] = $dateTimeString;


        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }


        $preparedData = $this->meetingDataPreparation($data);
        $result = Meeting::create($preparedData->toArray()[0]);
        $status = $this->meetingApprovedStatus();
        $meetingStatus = new MeetingStatus();
        $meetingStatus->meeting_id = $result->id;
        $meetingStatus->status_id = $status->id;
        $meetingStatus->operator_id = $data[0]['creatorID'];
        $meetingStatus->save();
        //        $result->statuses()->attach($status->id);

        return $result;
    }


    private function meetingDataPreparation(array|Collection $data)
    {
        if (is_array($data)) {
            $data = collect($data);
        }


        $data = $data->map(function ($item) {

            return [
                'title' => $item['title'] ?? null,
                'meeting_detail' => $item['meetingDetail'] ?? null,
                'meeting_number' => $item['meetingNumber'] ?? null,
                'isTemplate' => $item['isTemplate'] ?? false,
                'summary' => $item['summary'] ?? null,
                'creator_id' => $item['creatorID'],
                'meeting_type_id' => $item['meetingTypeID'],
                'ounit_id' => $item['ounitID'],
                'parent_id' => $item['parentID'] ?? null,
                'create_date' => now(),
                'start_time' => $item['startTime'] ?? null,
                'end_time' => $item['endTime'] ?? null,
                'invitation_date' => $item['invitationDate'] ?? null,
                'meeting_date' => $item['meetingDate'] ?? null,
                'reminder_date' => $item['reminderDate'] ?? null,
            ];

        });

        return $data;
    }


    public function ReplicateDatas(Meeting $lastMeeting, Meeting $newMeeting, OrganizationUnit $organizationUnit)
    {
        // Fetch the template meeting with its meeting members
        $meetingTemplate = Meeting::where('isTemplate', true)
            ->where('ounit_id', $organizationUnit->id)
            ->where('id', $lastMeeting->id)
            ->with('meetingMembers')
            ->first();

        if ($meetingTemplate) {
            foreach ($meetingTemplate->meetingMembers as $member) {
                // Check if this member already exists in the new meeting's members
                $existingMember = $newMeeting->meetingMembers->where('employee_id', $member->employee_id)->first();

                // If the member doesn't exist, replicate and add them to the new meeting
                if (!$existingMember) {
                    $newMember = $member->replicate(['laravel_through_key']); // Create a copy of the member
                    $newMember->meeting_id = $newMeeting->id; // Assign the new meeting's ID
                    $newMember->save(); // Save the replicated member
                }
            }
        }

        return $newMeeting->meetingMembers;
    }


    public function meetingApprovedStatus()
    {
        return Cache::rememberForever('approved_meeting_status', function () {
            return Meeting::GetAllStatuses()
                ->firstWhere('name', MeetingStatusEnum::APPROVED->value);
        });
    }

    public function meetingCancelStatus()
    {
        return Cache::rememberForever('canceled_meeting_status' , function () {
            return Meeting::GetAllStatuses()
                ->firstWhere('name' , MeetingStatusEnum::CANCELED->value);
        });
    }


}
