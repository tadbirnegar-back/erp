<?php

namespace Modules\EMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingMember;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\OUnitMS\app\Models\OrganizationUnit;

class RecruitmentStatusCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MeetingMemberTrait, RecruitmentScriptTrait;

    public RecruitmentScript $rs;


    /**
     * Create a new job instance.
     */
    public function __construct(RecruitmentScript $rs)
    {
        $this->rs = $rs;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $organ = OrganizationUnit::find($this->rs->organization_unit_id);

        $positionTitle = $this->rs->position->name;
        $mrInfo = $this->getMrIdUsingPositionTitle($positionTitle);

        $mrId = $mrInfo['id'];

        $meetingTemplate = Meeting::where('isTemplate', true)
            ->where('ounit_id', $organ->id)
            ->with(['meetingMembers' => function ($query) use ($mrId) {
                $query->where('mr_id', $mrId);
            }])->first();


        if ($meetingTemplate) {

            if (!$meetingTemplate->meetingMembers) {
                $meetingMember = $meetingTemplate->meetingMembers[0];

                $employee = Employee::find($meetingMember->employee_id);
                $employee->load('user.activeRecruitmentScript');
                $activeRs = $employee->user->activeRecruitmentScript;
                if (!empty($activeRs)) {


                    $statusAzlId = $this->terminatedRsStatus()->id;


                    \DB::table('recruitment_script_status')
                        ->insert([
                            'status_id' => $statusAzlId,
                            'recruitment_script_id' => $activeRs[0]->id,
                        ]);

                    //NewUser Operation
                    $employee = Employee::find($this->rs->employee_id);


                    MeetingMember::find($meetingMember->id)->update([
                        'employee_id' => $employee->id,
                    ]);
                    $meetingMember->employee_id = $employee->id;
                    $meetingMember->save();
                }
            } else {
                \Log::info("injam");
                $employee = Employee::find($this->rs->employee_id);

                $user = $employee->load('user');

                MeetingMember::create([
                    'employee_id' => $user->user->id,
                    'meeting_id' => $meetingTemplate->id,
                    'mr_id' => $mrId,
                ]);

            }
        }
    }
}
