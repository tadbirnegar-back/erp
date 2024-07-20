<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Models\ScriptApprovingList;

trait ApprovingListTrait
{
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
                        $query->whereHas('status', function ($query) use ($statusID) {
                            $query->where('id', $statusID);
                        });
                    });
                }]);

        $result = $query->paginate($perPage, page: $page);

        return $result;
    }


}
