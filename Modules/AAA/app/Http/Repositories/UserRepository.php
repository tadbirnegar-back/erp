<?php

namespace Modules\AAA\app\Http\Repositories;

use Modules\AAA\app\Models\User;

class UserRepository
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function isPersonUser(int $personID)
    {
        $result = $this->user::where('person_id', '=', $personID)->first();
        return $result;
    }

    public function store(array $data)
    {

        try {
            /**
             * @var User $user
             */
            $user = new $this->user;
            $user->mobile = $data['mobile'];
            $user->email = $data['email'] ?? null;
            $user->username = $data['username'] ?? null;
            $user->person_id = $data['personID'];
            $user->password = bcrypt($data['password']);

            $user->save();
            $user->roles()->sync($data['roles']);
            $status = $this->user::GetAllStatuses()->where('name', '=', 'فعال')->first();
            $user->statuses()->attach($status->id);
            return $user;
        } catch (\Exception $e) {
            return $e;
        }

    }

    public function show(int $id)
    {
        $user = $this->user::with('person.avatar','person.personable','person.status','status','roles')->findOrFail($id);

        return $user;
    }

    public function update(array $data, int $id)
    {
        try {

            $user = $this->user::findOrFail($id);
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
