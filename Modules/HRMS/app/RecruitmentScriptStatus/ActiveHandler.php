<?php

namespace Modules\HRMS\app\RecruitmentScriptStatus;

use Carbon\Carbon;
use Modules\AAA\app\Models\User;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\SubAccount;
use Modules\HRMS\app\Contracts\StatusHandlerInterface;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Jobs\ExpireScriptJob;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\RecruitmentScriptStatus;
use Modules\HRMS\app\Notifications\ApproveRsNotification;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

class ActiveHandler implements StatusHandlerInterface
{
    use RecruitmentScriptTrait, AccountTrait;

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
            $this->createAccAccountForEmployee();
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
        $ounit = $this->script->ounit->load([
            'ancestorsAndSelf' => fn($query) => $query->whereNotIn('unitable_type', [StateOfc::class, TownOfc::class])
        ]);

        $filteredAndSorted = $ounit->ancestorsAndSelf;

        $orderedNames = $filteredAndSorted->pluck('name')->reverse()->join('،');
        $position = $this->script->position;
        $person = $Notifibleuser->person;
        $Notifibleuser->notify((new ApproveRsNotification($person->display_name, $position?->name, $orderedNames))->onQueue('default'));
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

    public function createAccAccountForEmployee()
    {
        if ($this->script->organizationUnit->unitable_type == VillageOfc::class) {

            $parentAccount = Account::where('chain_code', 31101)->where('accountable_type', SubAccount::class)->first();

            $largest = Account::where('chain_code', 'LIKE', '31101%')
//                ->where('entity_type', $person->personable_type)
                ->where('ounit_id', $this->script->organizationUnit->id)
                ->orderByRaw('CAST(chain_code AS UNSIGNED) DESC')
                ->withoutGlobalScopes()
                ->first();

            $accData = [
                'entityID' => $this->script->employee_id,
                'entityType' => Employee::class,
                'name' => 'حقوق پرداختنی' . ' ' . $this->script->person->display_name . ' - ' . $this->script->person->national_code,
                'ounitID' => $this->script->organizationUnit->id,
                'segmentCode' => addWithLeadingZeros($largest?->segment_code ?? '000', 1),
                'chainCode' => $parentAccount->chain_code . addWithLeadingZeros($largest?->segment_code ?? '000', 1),
                'categoryID' => $parentAccount->category_id,
            ];
            $this->firstOrStoreAccount($accData, $parentAccount, 1);
        }
    }
}
