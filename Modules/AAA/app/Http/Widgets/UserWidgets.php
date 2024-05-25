<?php

namespace Modules\AAA\app\Http\Widgets;

use Modules\AAA\app\Models\User;

class UserWidgets
{
    public static function getUserInfo(User $user)
    {
        $user->load('person.avatar',
            'roles',
            'person.workForce.workforceable.recruitmentScripts.organizationUnit',
            'person.workForce.workforceable.recruitmentScripts.position');

        $data = [
            'person' => $user->person,
            'roles' => $user->roles,
            'position' => $user->person->workForce->workforceable->recruitmentScripts()->whereHas('status', function ($query) {
        $query->where('name', 'فعال')
            ->where('recruitment_script_status.create_date', function ($subQuery) {
                $subQuery->selectRaw('MAX(create_date)')
                    ->from('recruitment_script_status')
                    ->whereColumn('recruitment_script_id', 'recruitment_scripts.id');
            });
    })->with(['level', 'position', 'organizationUnit.unitable'])->get(),
        ];

        return $data;
    }

    public static function calendar(User $user)
    {
        return null;
    }

    public static function userOunits(User $user)
    {
        $user->load('organizationUnits.unitable','organizationUnits.ancestors');

        return $user->organizationUnits;
    }




}
