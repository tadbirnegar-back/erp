<?php

namespace Modules\HRMS\app\RecruitmentScriptStatus;

use Carbon\Carbon;
use Modules\AAA\app\Models\User;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\SubAccount;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingMember;
use Modules\EMS\app\Models\MeetingType;
use Modules\EMS\app\Models\MR;
use Modules\HRMS\app\Contracts\StatusHandlerInterface;
use Modules\HRMS\app\Http\Enums\PositionEnum;
use Modules\HRMS\app\Http\Enums\RecruitmentScriptStatusEnum;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Jobs\ExpireScriptJob;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\RecruitmentScriptStatus;
use Modules\HRMS\app\Models\ScriptApprovingList;
use Modules\HRMS\app\Notifications\ApproveRsNotification;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

class ActiveHandler implements StatusHandlerInterface
{
    use RecruitmentScriptTrait, AccountTrait, MeetingMemberTrait, MeetingTrait, ApprovingListTrait;

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
            $this->changePendingScriptsToNewUser();
            $this->setOldHeyaatMemberPendingForTerminate();
            $this->setOldHeadAsPendingForTerminate();
            $this->updateHeadByNewScript();
//            $this->notifyNewUser();
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

    public function setOldHeyaatMemberPendingForTerminate()
    {
        $script = $this->script;
        $positionEnum = PositionEnum::tryFrom($script->position->name);

        if ($positionEnum && $positionEnum->isHeyaatMemberPosition()) {
            $this->setUserAsMM();
        }


    }

    public function setUserAsMM()
    {

        $script = $this->script;
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


                    $statusAzlId = $this->pendingTerminateRsStatus();

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

            $existingAccount = Account::where('parent_id', $parentAccount->id)
                ->where('entity_id', $this->script->employee_id)
                ->where('entity_type', Employee::class)
                ->where('ounit_id', $this->script->organizationUnit->id)
                ->doesntExist();

            $script = $this->script;
            if ($existingAccount) {
//                $largest = Account::where('chain_code', 'LIKE', '31101%')
////                ->where('entity_type', $person->personable_type)
////                    ->where('ounit_id', $this->script->organizationUnit->id)
//                    ->where(function ($query) use ($script) {
//                        $query->where('ounit_id', $script->organizationUnit->id)
//                            ->orWhereNull('ounit_id');
//                    })
//                    ->orderByRaw('CAST(chain_code AS UNSIGNED) DESC')
//                    ->withoutGlobalScopes()
//                    ->activeInactive()
//                    ->first();
                $largest = $this->latestAccountByChainCode($parentAccount->chain_code, $this->script->organizationUnit->id);

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

    public function changePendingScriptsToNewUser()
    {
        $oldScript = RecruitmentScript::query()
            ->finalStatus()
            ->where('organization_unit_id', $this->script->organization_unit_id)
            ->where('position_id', $this->script->position_id)
            ->where('script_type_id', $this->script->script_type_id)
            ->where('job_id', $this->script->job_id)
            ->where('statuses.name', '=', RecruitmentScriptStatusEnum::ACTIVE->value)
            ->with('user')
            ->first();
        $newUser = $this->script->user;

        if ($oldScript) {
            $oldUser = $oldScript->user;
            $approvings = ScriptApprovingList::where('assigned_to', $oldUser->id)
                ->whereHas('status', function ($query) {
                    $query->whereIn('name', [
                        $this::$currentUserPendingStatus,
                        $this::$pendingStatus,
                    ]);
                })
                ->get();

            $approvings->each(function ($approving) use ($newUser) {
                $approving->assigned_to = $newUser->id;
                $approving->save();
            });

        }
    }
}
