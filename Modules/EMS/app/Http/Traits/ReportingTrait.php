<?php

namespace Modules\EMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\EMS\app\Http\Enums\MeetingStatusEnum;
use Modules\EMS\app\Http\Enums\MeetingTypeEnum;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingStatus;
use Modules\OUnitMS\app\Models\OrganizationUnit;

trait ReportingTrait
{

    public function reportMyself($user)
    {
        $meetingTypes = $this->meetingTypeFinder($user->activeFreeZoneRecruitmentScript, $user->activeDistrictRecruitmentScript);
        if($user->activeFreeZoneRecruitmentScript->isNotEmpty())
        {
            $this -> reportMyselfFreeZone($user->activeFreeZoneRecruitmentScript);
        }
    }

    private function meetingTypeFinder($freezone, $dirstrict)
    {
        $heyaatMeeting = MeetingTypeEnum::HEYAAT_MEETING->value;
        $freezoneMeeting = MeetingTypeEnum::FREE_ZONE->value;

        $meetingtypes = [];

        if ($freezone->isNotEmpty()) {
            $meetingtypes[] = $freezoneMeeting;
        }
        if ($dirstrict->isNotEmpty()) {
            $meetingtypes[] = $heyaatMeeting;
        }
        return $meetingtypes;
    }

    private function reportMyselfFreeZone($user)
    {
        $userRc = $user->activeFreeZoneRecruitmentScript;
        $districts = $this->findDistrictsByFreeZone($userRc);
    }

    private function findDistrictsByFreeZone($rc)
    {
        $rc =
    }
}
