<?php

namespace Modules\HRMS\app\RecruitmentScriptStatus;

use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Contracts\StatusHandlerInterface;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Notifications\ScriptExpireNotification;

class TerminateHandler implements StatusHandlerInterface
{
    use UserTrait;

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
//        $this->notifyScriptUser();

    }

    public function notifyScriptUser()
    {
        $user = $this->script->user;
        $person = $user->person;
        $scriptTypeName = $this->script->scriptType ? $this->script->scriptType->title : 'نامعلوم';
        $ounit = $this->script->ounit;

        $ExpDateEng = \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($this->script->expire_date)); // 1395-02-19
        $ExpDateFarsi = \Morilog\Jalali\CalendarUtils::convertNumbers($ExpDateEng); // ۱۳۹۵-۰۲-۱۹


        $user->notify(new ScriptExpireNotification($person->display_name, $ExpDateFarsi, $scriptTypeName, $ounit->name));
    }
}
