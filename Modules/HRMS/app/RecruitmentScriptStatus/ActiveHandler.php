<?php

namespace Modules\HRMS\app\RecruitmentScriptStatus;

use Carbon\Carbon;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Contracts\StatusHandlerInterface;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Jobs\ExpireScriptJob;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\RecruitmentScriptStatus;
use Modules\HRMS\app\Notifications\ApproveRsNotification;

class ActiveHandler implements StatusHandlerInterface
{
    use RecruitmentScriptTrait;

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
            $this->addRolesOfScriptToUser();
            $this->activateScriptUser();
            $this->setOldHeadAsPendingForTerminate();
            $this->updateHeadByNewScript();
            $this->notifyNewUser();
            $this->dispatchQueueForExpireScript();
        });
    }

    public function addRolesOfScriptToUser(): void
    {
        $script = $this->script;
        $roles = $script->position->roles;
        $scriptUser = $script->user;
        $scriptUser->roles()->attach($roles->pluck('id')->toArray());

    }

    public function activateScriptUser()
    {
        $userActiveStatus = User::GetAllStatuses()->firstWhere('name', 'فعال');
        $scriptUser = $this->script->user;

        $scriptUser->statuses()->attach($userActiveStatus->id);

    }

    public function setOldHeadAsPendingForTerminate()
    {
        $script = $this->script;

        if ($script->scriptType->isHeadable) {
            $script->load('organizationUnit.person.employee');
            if ($script->organizationUnit?->person) {
                $oldScript = RecruitmentScript::where('organization_unit_id', $script->organizationUnit->id)
                    ->where('employee_id', $script->organizationUnit->head->employee->id)
                    ->first();

                if ($oldScript) {
                    RecruitmentScriptStatus::create([
                        'recruitment_script_id' => $oldScript->id,
                        'status_id' => $this->pendingTerminateRsStatus()->id,
                        'operator_id' => $this->user?->id ?? null,
                    ]);
                }
            }

        }
    }

    public function updateHeadByNewScript()
    {
        $script = $this->script;

        if ($script->scriptType->isHeadable) {
            $user = $script->employee->person->user;
            $ounit = $script->organizationUnit;
            $ounit->head_id = $user->id;
            $ounit->save();
        }
    }

    public function notifyNewUser()
    {
        $Notifibleuser = $this->script->user;
        $ounit = $this->script->ounit;
        $position = $this->script->position;
        $person = $Notifibleuser->person;
        $Notifibleuser->notify(new ApproveRsNotification($person->display_name,$position->name,$ounit->name));
    }

    public function dispatchQueueForExpireScript()
    {
        $expireDate = $this->script->expire_date;
        // Ensure consistent timezone
        if (!is_null($expireDate)) {
            $expireDateCarbon = Carbon::parse($expireDate)->setTimezone(config('app.timezone'));

            ExpireScriptJob::dispatch($this->script->id)->delay($expireDateCarbon);

        }

    }
}
