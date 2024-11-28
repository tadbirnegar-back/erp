<?php

namespace Modules\EMS\app\Jobs;

use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingMember;
use Modules\EMS\app\Models\MeetingType;
use Modules\EMS\app\Models\MR;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Models\RecruitmentScript;

class RecruitmentStatusCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MeetingMemberTrait, RecruitmentScriptTrait, MeetingTrait;

    public int $rs;


    /**
     * Create a new job instance.
     */
    public function __construct(int $rs)
    {
        $this->rs = $rs;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();
            $script = RecruitmentScript::with('ounit', 'position', 'user')->find($this->rs);

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

                $oldUser = User::with(['activeRecruitmentScript' => function ($q) use ($organ, $script) {
                    $q->where('organization_unit_id', '=', $organ->id)
                        ->where('script_type_id', '=', $script->script_type_id)
                        ->where('position_id', '=', $script->position_id);
                }])->find($meetingMember->employee_id);

                $activeRs = $oldUser->activeRecruitmentScript;
                if ($activeRs->isNotEmpty()) {


                    $statusAzlId = $this->terminatedRsStatus();

                    $this->attachStatusToRs($statusAzlId, $activeRs->first());


                }
                if (is_null($meetingMember)) {
                    $mm = new MeetingMember();
                    $mm->employee_id = $script->user->id;
                    $mm->meeting_id = $meetingTemplate->id;
                    $mm->mr_id = $mr->id;
                    $mm->save();
                } else {
                    $meetingMember->employee_id = $script->user->id;
                    $meetingMember->save();
                }
            } else {

                $user = $script->user;

                $data['creatorID'] = $user->id;
                $data['meetingTypeID'] = MeetingType::where('title', 'الگو')->first()->id;
                $data['isTemplate'] = true;
                $data['ounitID'] = $organ->id;
                $meeting = $this->storeMeeting($data);

                MeetingMember::create([
                    'employee_id' => $user->user->id,
                    'meeting_id' => $meeting->id,
                    'mr_id' => $mr->id,
                ]);

            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->fail($e);
        }

    }
}
