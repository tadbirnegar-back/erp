<?php

namespace Modules\EMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MR;

trait MeetingMemberTrait
{

    public function bulkUpdateMeetingMembers(Collection|array $data, Meeting $meeting)
    {
        $dataToInsert = $this->meetingMemberDataPreparation($data, $meeting);

        $result = MR::upsert($dataToInsert->toArray(), ['id']);

        return $result;
    }

    private function meetingMemberDataPreparation(Collection|array $data, Meeting $meeting)
    {
        if (is_array($data)) {
            $data = collect($data);
        }

        $data = $data->map(function ($meetingMember) use ($meeting) {
            $mr = MR::where('title', $meetingMember['mrName'])->first();
            return [
                'id' => $meetingMember['meetingMemberID'] ?? null,
                'employee_id' => $meetingMember['userID'],
                'meeting_id' => $meeting->id,
                'mr_id' => $mr->id,

            ];
        });

        return $data;

    }
}
