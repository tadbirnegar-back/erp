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
            'position' => $user->person->workForce->workforceable->recruitmentScripts,
        ];

        return $data;
    }

    public static function calendar(User $user)
    {
        return null;
    }

}
