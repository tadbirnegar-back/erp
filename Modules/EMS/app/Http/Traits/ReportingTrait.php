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
    public function reportMyself($user , $request)
    {
        $meetingTypes = $this->meetingTypeFinder($user->activeFreeZoneRecruitmentScript, $user->activeDistrictRecruitmentScript);
        $freeZoneDistrictIds = [];
        $districtIds = [];
        if($user->activeFreeZoneRecruitmentScript->isNotEmpty())
        {
            $freeZoneDistrictIds =  $this -> findDistrictsByFreeZone($user->activeFreeZoneRecruitmentScript);
        }
        if($user->activeDistrictRecruitmentScript->isNotEmpty())
        {
            if(!$request->ounitID)
            {
                $districtIds =  $user->activeDistrictRecruitmentScript[0]->ounit->ancestorsAndSelf->pluck('unitable_id')->toArray();
            }else{
                $districtIds =  [$request->ounitID];
            }
        }
        $allDistrictIds = array_unique(array_merge($freeZoneDistrictIds, $districtIds));
        return $allDistrictIds;
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

    public function findDistrictsByFreeZone($rc)
    {
        // Execute the query
        $districts = $rc->load('getDistrictFromFreeZoneRc');
        return $districts[0]->getDistrictFromFreeZoneRc->pluck('unitable_id')->toArray();
    }
}
