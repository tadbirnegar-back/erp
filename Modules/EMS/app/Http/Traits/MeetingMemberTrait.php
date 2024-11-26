<?php

namespace Modules\EMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingMember;
use Modules\EMS\app\Models\MR;

trait MeetingMemberTrait
{

    public function bulkUpdateMeetingMembers(Collection|array $data, Meeting $meeting)
    {
        $dataToInsert = $this->meetingMemberDataPreparation($data, $meeting);

        $result = MeetingMember::upsert($dataToInsert->toArray(), ['id']);

        return $result;
    }

    private function meetingMemberDataPreparation(Collection|array $data, Meeting $meeting)
    {
        if (is_array($data)) {
            $data = collect($data);
        }

        $data = $data->map(function ($meetingMember) use ($meeting) {
            if (isset($meetingMember['mrName'])) {
                $mr = MR::where('title', $meetingMember['mrName'])->first();
            }
            return [
                'id' => $meetingMember['meetingMemberID'] ?? null,
                'employee_id' => $meetingMember['userID'],
                'meeting_id' => $meeting->id,
                'mr_id' => $mr?->id,

            ];
        });

        return $data;

    }


    public function getMrIdUsingPositionTitle($title)
    {
        $data = [
            "کارشناس مشورتی" => [
                "title" => "کارشناس مشورتی (کارشناس هیات تطبیق)",
                "id" => 2
            ],
            "بخشدار" => [
                "title" => "بخشدار (عضو هیات تطبیق)",
                "id" => 5
            ],
            "مسئول دبیرخانه" => [
                "title" => "مسئول دبیرخانه و دبیر تطبیق (کارشناس هیات تطبیق)",
                "id" => 9
            ],
            "نماینده قوه قضائیه" => [
                "title" => "نماینده قوه قضائیه (عضو هیات تطبیق)",
                "id" => 6
            ],
            "نماینده استانداری" => [
                "title" => "نماینده استانداری (کارشناس هیات تطبیق)",
                "id" => 8
            ],
            "عضو شورای شهرستان" => [
                "title" => "عضو شورای شهرستان (عضو هیات تطبیق)",
                "id" => 7
            ]
        ];

        return $data[$title] ?? null; // Return null if the title does not exist
    }
}
