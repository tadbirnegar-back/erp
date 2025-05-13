<?php

namespace Modules\AAA\app\Http\Traits;

use Modules\AAA\app\Models\Role;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Models\Position;
use Modules\PersonMS\app\Models\Person;

trait UserTrait
{

    public function isPersonUserCheck(Person $person): ?User
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

    public function storeUserOrUpdate(array $data)
    {
        $user = User::where('mobile', '=', $data['mobile'])->first();
        if(!$user){
            if(isset($data['email'])){
                $userWithSameEmail = User::where('email', '=', $data['email'])->first();
                if($userWithSameEmail)
                {
                    return ['status' => 404 , 'type' => 'email'];
                }
            }

            $user = new User();
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
            return ['user' => $user , 'status' => 200];
        }else{
            if($data['personID'] == $user->person_id){
                return ['user' => $user , 'status' => 200];
            }else{
                return ['status' => 404 , 'type' => 'mobile'];
            }
        }
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

        if (isset($data['statusID']) && $data['statusID'] != $status[0]->id) {
            $user->statuses()->attach($data['statusID']);
        }
        return $user;

    }

    public function mobileExists(string $mobile)
    {
        $user = User::with('latestStatus', 'person.avatar')->where('mobile', '=', $mobile)
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


    public function paswrodUpdater(User $user, $newPassword)
    {
        // Update the user's password
        $user->password = \Hash::make($newPassword);
        $user->save();

        return true;
    }


    public function updatePasswordWithCurrentPassword(User $user, $currentPassword, $newPassword)
    {
        if (\Hash::check($currentPassword, $user->password)) {
            $user->password = \Hash::make($newPassword);
            $user->save();
            return true;
        } else {
            return false;
        }

    }

    public function detachRolesByPosition(User $user, int $positionID): true
    {
        $roles = Position::with('roles')->find($positionID)->roles->pluck('id');
        $user->roles()->detach($roles->toArray());
        $a = $roles->pluck('id');
        $b = $user->allRoles->pluck('id');

// Track counts of $a
        $aCounts = $a->countBy();

// Use reject to remove items from $b based on counts
        $result = $b->reject(function ($value, $key) use (&$aCounts) {
            if ($aCounts->has($value) && $aCounts[$value] > 0) {
                $aCounts[$value]--; // Decrease the count
                return true;        // Remove this element
            }
            return false;           // Keep the element
        });

        $user->roles()->sync($result->toArray());

        return true;
    }
    public function attachRoleForEngineer($userID)
    {
        $statusID = Role::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
        $role = Role::where('name' , 'مهندس ساختمان')->firstOrCreate(['name' => 'مهندس ساختمان' , 'status_id' => $statusID]);
        $user = User::find($userID);
        $user->roles()->attach($role->id);
    }

}
