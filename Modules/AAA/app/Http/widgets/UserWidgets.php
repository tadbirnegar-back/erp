<?php

namespace Modules\AAA\app\Http\widgets;

use Modules\AAA\app\Models\User;

class UserWidgets
{
    public static function getUserInfo(User $user)
    {
        $user->load('person.avatar','roles');

        $data = [
            'person' => $user->person,
            'roles' => $user->roles,

        ];

        return $data;
    }

    public static function calendar(User $user)
    {
        return null;
    }

}
