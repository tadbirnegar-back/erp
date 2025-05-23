<?php

namespace Modules\AAA\app\Http\Repositories;

use Modules\AAA\app\Models\User;

class UserRepository
{
    protected User $user;



    public function isPersonUser(int $personID)
    {
        $result = User::where('person_id', '=', $personID)->first();
        return $result;
    }

    public function store(array $data)
    {

        try {
            /**
             * @var User $user
             */
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
            $status = User::GetAllStatuses()->where('name', '=', 'فعال')->first();
            $user->statuses()->attach($status->id);
            return $user;
        } catch (\Exception $e) {
            return $e;
        }

    }

    public function show(int $id)
    {
        $user = User::with('person.avatar','person.personable','person.status','status','roles')->findOrFail($id);

        return $user;
    }

    public function update(array $data, int $id)
    {
        try {

            $user = User::findOrFail($id);
            $user->mobile = $data['mobile'];
            $user->email = $data['email'] ?? null;
            $user->username = $data['username'] ?? null;
            if (isset($data['password'])) {
                $user->password = bcrypt($data['password']);

            }
            $user->save();

           $person= $user->person ;
            $person->profile_picture_id = $data['avatar'] ?? null;
            $person->save();
            $user->roles()->sync($data['roles']);
            $status = $user->status;

            if ($data['statusID'] != $status[0]->id) {
                $user->statuses()->attach($data['statusID']);
            }
            return $user;
        } catch (\Exception $e) {
            return $e;
        }

    }
}
