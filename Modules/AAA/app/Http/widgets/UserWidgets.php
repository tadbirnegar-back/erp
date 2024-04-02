<?php

namespace Modules\AAA\app\Http\widgets;

use Modules\AAA\app\Models\User;

class UserWidgets
{
    public static function getUserInfo(int $userID)
    {
        $user = User::with('person.avatar','roles')->find($userID);

        return $user;
    }

}
