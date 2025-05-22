<?php

namespace Modules\HRMS\app\RecruitmentScriptStatus;

use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MR;
use Modules\HRMS\app\Contracts\StatusHandlerInterface;
use Modules\HRMS\app\Http\Enums\PositionEnum;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Notifications\PayanKhedmatRsNotification;

class CancelHandler implements StatusHandlerInterface
{
    use UserTrait, RecruitmentScriptTrait, MeetingMemberTrait;

    private RecruitmentScript $script;
    private ?User $user;

    public function __construct(RecruitmentScript $script, ?User $user = null)
    {
        $this->script = $script;
        $this->user = $user;
    }

    public function execute(): void
    {
        $this->detachRolesByPosition($this->script->user, $this->script->position_id);
        $this->removeUserFromHeyaatMemberTemplate();
        $this->notifyScriptUser();
        $this->AddFinishDate($this->script);
    }

    public function notifyScriptUser()
    {
        $user = $this->script->user;
        $person = $user->person;

        $ounit = $this->script->ounit;

        $user->notify((new PayanKhedmatRsNotification($person->display_name, $ounit->name, $this->script->position->name))->onQueue('default'));
    }

    public function AddFinishDate($script): void
    {
        $this->UpdateFinishDate($script, now());
    }

    public function removeUserFromHeyaatMemberTemplate()
    {
        $script = $this->script;
        $positionEnum = PositionEnum::tryFrom($script->position->name);

        if ($positionEnum && $positionEnum->isHeyaatMemberPosition()) {
            $script = $this->script;
            $user = $script->user;

            $organ = $script->ounit;

            $positionTitle = $script->position->name;
            $mrInfo = $this->getMrIdUsingPositionTitle($positionTitle);

            $mrId = $mrInfo['title'];
            $mr = MR::where('title', $mrId)->first();
            $meetingTemplate = Meeting::where('isTemplate', true)
                ->where('ounit_id', $organ->id)
                ->with(['meetingMembers' => function ($query) use ($mr, $user) {
                    $query
                        ->where('employee_id', $user->id)
                        ->where('mr_id', $mr->id);
                }])->first();

            if ($meetingTemplate) {

                $meetingMember = $meetingTemplate->meetingMembers->first();
                if (!is_null($meetingMember)) {
                    $meetingMember->employee_id = null;
                    $meetingMember->save();
                }
            }
        }
    }

}
