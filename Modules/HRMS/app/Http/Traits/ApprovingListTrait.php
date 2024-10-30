<?php

namespace Modules\HRMS\app\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Models\ConfirmationType;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptApprovingList;

trait ApprovingListTrait
{
    use RecruitmentScriptTrait, EmployeeTrait;

    private static string $currentUserPendingStatus = 'درانتظار تایید من';
    private static string $pendingStatus = 'درانتظار تایید';
    private static string $approvedStatus = 'تایید شده';

    public function approvingListPendingIndex(User $user)
    {

        $result = ScriptApprovingList::where('assigned_to', $user->id)
            ->WhereHas('status', function ($query) {
                $query->where('name', self::$currentUserPendingStatus);
            })
            ->with([
                'assignedTo',
                'script.employee.person'
                , 'status',
                'script.scriptType'
                , 'script.hireType'])->distinct()->get();


        return $result;
    }

    public function approvingStore(RecruitmentScript $rs)
    {
        $conformationTypes = $rs->scriptType?->confirmationTypes;
        $approves = [];
        if (!is_null($conformationTypes) && $conformationTypes->isNotEmpty()) {

            $conformationTypes->each(function (ConfirmationType $confirmationType) use (&$approves, $rs) {
                $optionID = $confirmationType->pivot->option_id ?? null;
                $optionType = $confirmationType->pivot->option_type;
                $approveList = $optionType::generateApprovers($optionID, $rs);
                $approves[] = $approveList;
            });
            $preparedData = $this->prepareApprovingData($approves, $rs);
            $result = ScriptApprovingList::insert($preparedData->toArray());
            return $result;
        }
        return null;
    }

    private function prepareApprovingData(array|Collection $data, RecruitmentScript $script)
    {
        if (is_array($data)) {
            $data = collect($data);
        }
        $data = $data->flatten(1);
        $currentUserPendingStatus = self::pendingForCurrentUserStatus();
        $pendingStatus = self::pendingStatus();

        $data = $data->where('assignedUserID', '!=', null);

        $counter = 1; // Manual counter

        $data = $data->map(function ($item) use ($script, $currentUserPendingStatus, $pendingStatus, &$counter) {
            $status = $counter == 1 ? $currentUserPendingStatus : $pendingStatus;
            $result = [
                'id' => $item['appID'] ?? null,
                'script_id' => $script->id,
                'priority' => $counter,
                'assigned_to' => $item['assignedUserID'] ?? null,
                'approver_id' => $item['approverID'] ?? null,
                'status_id' => $status->id,
                'create_date' => Carbon::now(),
            ];
            $counter++; // Increment the counter
            return $result;
        });

        return $data;
    }

    public static function pendingForCurrentUserStatus()
    {
        return ScriptApprovingList::GetAllStatuses()->firstWhere('name', '=', self::$currentUserPendingStatus);
    }

    public static function pendingStatus()
    {
        return ScriptApprovingList::GetAllStatuses()->firstWhere('name', '=', self::$pendingStatus);
    }

    public function approveScript(RecruitmentScript $script, User $user)
    {
        $approvingList = $script->pendingScriptApproving()->where('assigned_to', $user->id)->first();
        if (!$approvingList) {
            return null;
        }
        $approvingList->status_id = self::approvedStatus()->id;
        $approvingList->update_date = Carbon::now();
        $approvingList->approver_id = $user->id;
        $approvingList->save();

        $nextApprovingList = $script->approvers()->where('priority', $approvingList->priority + 1)->first();
        if ($nextApprovingList) {
            $nextApprovingList->status_id = self::pendingForCurrentUserStatus()->id;
            $nextApprovingList->save();
        } else {
            $script->status()->attach($this->activeRsStatus()->id);

            $script->load([
                'organizationUnit',
                'scriptType.confirmationTypes',
                'employee.workForce',
                'position.roles',
            ]);
            if ($script->scriptType->isHeadable) {
                $user = $script->employee->person->user;
                $ounit = $script->organizationUnit;
                $ounit->head_id = $user->id;
                $ounit->save();
            }


            $status = Employee::GetAllStatuses()->firstWhere('id', $script->scriptType->employee_status_id);


            $script->employee->workForce->statuses()->attach($status->id);

            $position = $script->position;
            $roles = $position->roles;
            $scriptUser = $script->employee->person->user;
            $userActiveStatus = User::GetAllStatuses()->firstWhere('name', 'فعال');
//            $hasRole = $scriptUser->roles()->where('role_id', $role->id)->exists();

            // Attach the role to the user if they do not have it
//            if (!$hasRole) {
            $scriptUser->roles()->sync($roles->pluck('id')->toArray());
//            }

            $scriptUser->statuses()->attach($userActiveStatus->id);

        }

        return $approvingList;


    }

    public static function approvedStatus()
    {
        return ScriptApprovingList::GetAllStatuses()->firstWhere('name', '=', self::$approvedStatus);
    }

}
