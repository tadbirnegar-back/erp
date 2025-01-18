<?php

namespace Modules\HRMS\app\RecruitmentScriptStatus;

use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Contracts\StatusHandlerInterface;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Notifications\DeclineRsNotification;

class DeclineHandler implements StatusHandlerInterface
{

    private RecruitmentScript $script;
    private ?User $user;

    public function __construct(RecruitmentScript $script, ?User $user = null)
    {
        $this->script = $script;
        $this->user = $user;
    }

    public function execute(): void
    {
        \DB::transaction(function () {

            $this->notifyScriptUser();
        });
    }

    public function notifyScriptUser()
    {


        $notifibleUser = $this->script->user;

        $person = $notifibleUser->person;

        $notifibleUser->notify(new DeclineRsNotification($person->display_name));
    }
}
