<?php

namespace Modules\EMS\app\Listeners;

use DB;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Events\RecruitmentStatusCreatedEvent;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingMember;
use Modules\EMS\app\Models\MeetingType;
use Modules\EMS\app\Models\MR;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Http\Traits\ScriptTypeTrait;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;

class RecruitmentStatusCreatedListener
{
    use RecruitmentScriptTrait, ScriptTypeTrait, MeetingMemberTrait, MeetingTrait;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RecruitmentStatusCreatedEvent $event): void
    {
        $recstatus = $event->recStatus;
        $ApprovalStatus = $this->activeRsStatus()->id;
        if ($recstatus->status_id == $ApprovalStatus) {
            $recruitmentScript = RecruitmentScript::find($recstatus->recruitment_script_id);

            $scriptTypeId = ScriptType::where('title', 'انتصاب هیئت تطبیق')->first()->id;

            $scriptTypeDabir = ScriptType::where('title', 'انتصاب دبیر')->first()->id;

            $scriptTypeBakhshdar = ScriptType::where('title', 'انتصاب بخشدار')->first()->id;

            if ($recruitmentScript->script_type_id == $scriptTypeId || $recruitmentScript->script_type_id == $scriptTypeDabir || $recruitmentScript->script_type_id == $scriptTypeBakhshdar) {
//                RecruitmentStatusCreatedJob::dispatch($recruitmentScript->id);
                $this->setUserAsMM($recruitmentScript->id);
            }
        }
    }

    public function setUserAsMM(int $id)
    {
        try {
            DB::beginTransaction();
            $script = RecruitmentScript::with('ounit', 'position', 'user')->find($id);
            $user = $script->user;

            $organ = $script->ounit;

            $positionTitle = $script->position->name;
            $mrInfo = $this->getMrIdUsingPositionTitle($positionTitle);

            $mrId = $mrInfo['title'];
            $mr = MR::where('title', $mrId)->first();
            $meetingTemplate = Meeting::where('isTemplate', true)
                ->where('ounit_id', $script->organization_unit_id)
                ->with(['meetingMembers' => function ($query) use ($mr) {
                    $query->where('mr_id', $mr->id);
                }])->first();


            if ($meetingTemplate) {

                $meetingMember = $meetingTemplate->meetingMembers->first();
                if (is_null($meetingMember)) {
                    $mm = new MeetingMember();
                    $mm->employee_id = $user->id;
                    $mm->meeting_id = $meetingTemplate->id;
                    $mm->mr_id = $mr->id;
                    $mm->save();
                } else {

                    $oldUser = User::with(['activeRecruitmentScript' => function ($q) use ($organ, $script) {
                        $q->where('organization_unit_id', '=', $organ->id)
                            ->where('script_type_id', '=', $script->script_type_id)
                            ->where('position_id', '=', $script->position_id);
                    }])->find($meetingMember->employee_id);

                    $activeRs = $oldUser->activeRecruitmentScript;
                    if ($activeRs->isNotEmpty()) {


                        $statusAzlId = $this->terminatedRsStatus();

                        $this->attachStatusToRs($activeRs->first(), $statusAzlId);


                    }
                    $meetingMember->employee_id = $user->id;
                    $meetingMember->save();
                }
            } else {


                $data['creatorID'] = $user->id;
                $data['meetingTypeID'] = MeetingType::where('title', 'الگو')->first()->id;
                $data['isTemplate'] = true;
                $data['ounitID'] = $organ->id;
                $meeting = $this->storeMeeting($data);

                MeetingMember::create([
                    'employee_id' => $user->id,
                    'meeting_id' => $meeting->id,
                    'mr_id' => $mr->id,
                ]);

            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
//            $this->fail($e);
        }
    }
}
