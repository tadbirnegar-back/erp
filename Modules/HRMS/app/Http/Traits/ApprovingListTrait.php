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
        $searchTerm = $data['search'] ?? null;

        $query = ScriptApprovingList::where('assigned_to', $user->id)
            ->with([

                'script' => function ($query) use ($scriptTypeID) {
                    $query->when($scriptTypeID, function ($query) use ($scriptTypeID) {
                        $query->where('script_type_id', $scriptTypeID);
                    });
                }

                , 'approver'

                , 'status' => function ($query) use ($statusID) {
                    $query->when($statusID, function ($query) use ($statusID) {
//                        $query->whereHas('status', function ($query) use ($statusID) {
                        $query->where('id', $statusID);
//                        });
                    });
                }]);

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
            array_push($approves, $approveList);
        });

        $preparedData = $this->prepareApprovingData($approves);
        $result = ScriptApprovingList::insert($preparedData);
        return $result;
    }

    private function prepareApprovingData(array|Collection $data,)
    {
        if (is_array($data)) {
            $data = collect($data);
        }

        $data = $data->map(fn($item) => [
            'id' => $item['appID'] ?? null,
            'script_id' => $item['scriptID'],
            'priority' => $item['priority'],
            'assigned_to' => $item['assignedUserID'],
            'approver_id' => $item['approverID'] ?? null,
            'status_id' => $item['statusID'],

        ]);

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
