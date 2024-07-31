<?php

namespace Modules\HRMS\app\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Models\ConfirmationType;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptApprovingList;

trait ApprovingListTrait
{

    private static string $currentUserPendingStatus = 'درانتظار تایید من';
    private static string $pendingStatus = 'درانتظار تایید';

    public function approvingListIndex(array $data, User $user)
    {
        $statusID = $data['statusID'] ?? null;
        $scriptTypeID = $data['scriptTypeID'] ?? null;
        $perPage = $data['perPage'] ?? 10;
        $page = $data['page'] ?? 1;
        $searchTerm = $data['name'] ?? null;

        $query = ScriptApprovingList::where('assigned_to', $user->id)
            ->where(function ($query) {
                $query->WhereHas('status', function ($query) {
                    $query->where('name', self::$currentUserPendingStatus);
                })
                    ->orWhere('status_id', '!=', null);
            })
            ->when($statusID, function ($query) use ($statusID) {
                $query->where('status_id', $statusID);
            })
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->whereHas('employee.person', function ($query) use ($searchTerm) {

                    $query->whereRaw('MATCH(display_name) AGAINST(?)', [$searchTerm])
                        ->orWhere('display_name', 'LIKE', '%' . $searchTerm . '%')
                        ->selectRaw('persons.*, MATCH(display_name) AGAINST(?) AS relevance', [$searchTerm])
                        ->orderByDesc('relevance');
                });
            })
            ->when($scriptTypeID, function ($query)use ($scriptTypeID){
                $query->whereHas('script', function ($query)use ($scriptTypeID){
                    $query->where('script_type_id',$scriptTypeID);
                });
            })
            ->with([
                'assignedTo',
                'script.employee.person'
                ,'status',
                'script.scriptType'
            ,'script.hireType'])->distinct();

        $result = $query->paginate($perPage, page: $page);

        return $result;
    }

    public function approvingStore(RecruitmentScript $rs)
    {
        $conformationTypes = $rs->scriptType->confirmationTypes;
        $approves = [];

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

    private function prepareApprovingData(array|Collection $data, RecruitmentScript $script)
    {
        if (is_array($data)) {
            $data = collect($data);
        }

        $data = $data->flatten(1);

        $currentUserPendingStatus = self::pendingForCurrentUserStatus();
        $pendingStatus = self::pendingStatus();

        $data = $data->where('assignedUserID', '!=', null);

        $data = $data->map(function ($item, $key) use ($script, $currentUserPendingStatus, $pendingStatus) {

            $status = $key == 0 ? $currentUserPendingStatus : $pendingStatus;
            return [
                'id' => $item['appID'] ?? null,
                'script_id' => $script->id,
                'priority' => $key + 1,
                'assigned_to' => $item['assignedUserID'] ?? null,
                'approver_id' => $item['approverID'] ?? null,
                'status_id' => $status->id,
                'create_date' => Carbon::now(),

            ];
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

}
