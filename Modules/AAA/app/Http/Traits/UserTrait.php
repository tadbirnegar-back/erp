<?php

namespace Modules\AAA\app\Http\Traits;

use Modules\AAA\app\Models\User;
use Modules\PersonMS\app\Models\Person;

trait UserTrait
{


    public function isPersonUserCheck(Person $person): User
    {
        $result = User::where('person_id', '=', $person->id)->first();
        return $result;
    }

    public function storeUser(array $data)
    {


        $user = new User;
        $user->mobile = $data['mobile'];
        $user->email = $data['email'] ?? null;
        $user->username = $data['username'] ?? null;
        $user->person_id = $data['personID'];
        $user->password = bcrypt($data['password']);

        $user->save();
        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }
        $status = $this->activeUserStatus();
        $user->statuses()->attach($status->id);
        return $user;

    }

    public function showUser(User $user)
    {
        $user->load('person.avatar', 'person.personable', 'person.status', 'status', 'roles');

        return $user;
    }

    public function updateUser(array $data, User $user)
    {


        $user->mobile = $data['mobile'];
        $user->email = $data['email'] ?? null;
        $user->username = $data['username'] ?? null;
        if (isset($data['password'])) {
            $user->password = bcrypt($data['password']);

        }
        $user->save();

        $person = $user->person;
        $person->profile_picture_id = $data['avatar'] ?? null;
        $person->save();
        if (isset($data['roles'])) {

            $data['roles'] = json_decode($data['roles']);
            $user->roles()->sync($data['roles']);

        }

        $status = $user->status;

        if (isset($data['statusID'])&&$data['statusID'] != $status[0]->id) {
            $user->statuses()->attach($data['statusID']);
        }
        return $user;

    }

    public function mobileExists(string $mobile)
    {
        $user = User::with('person.avatar')->where('mobile', '=', $mobile)
        ->first();

        return $user;
    }

    public function activeUserStatus()
    {
        return User::GetAllStatuses()->firstWhere('name', '=', 'فعال');
    }

    public function inactiveUserStatus()
    {
        return User::GetAllStatuses()->firstWhere('name', '=', 'غیرفعال');
    }

    public function allUserStats()
    {
        return User::GetAllStatuses();
    }
}
