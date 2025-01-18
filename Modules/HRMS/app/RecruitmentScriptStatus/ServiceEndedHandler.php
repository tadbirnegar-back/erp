<?php

namespace Modules\HRMS\app\RecruitmentScriptStatus;

use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Contracts\StatusHandlerInterface;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Notifications\PayanKhedmatRsNotification;

class ServiceEndedHandler implements StatusHandlerInterface
{
    use UserTrait , RecruitmentScriptTrait;

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
        $this->notifyScriptUser();
        $this->AddFinishDate($this -> script);

    }

    public function notifyScriptUser()
    {
        $user = $this->script->user;
        $person = $user->person;

        $ounit = $this->script->ounit;

        $user->notify(new PayanKhedmatRsNotification($person->display_name, $ounit->name, $this->script->position->name));
    }

    public function AddFinishDate($script): void
    {
        $this->UpdateFinishDate($script, now());

    }
}
