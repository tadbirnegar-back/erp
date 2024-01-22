<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\Permission;
use Modules\AAA\app\Models\Role;
use Modules\AAA\app\Models\User;
use Modules\AddressMS\app\Models\Address;
use Modules\FileMS\app\Models\File;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;

class testController extends Controller
{
    public function run(): void
    {
        DB::enableQueryLog();

//        $add = Address::with('city.state.country')->find(1);
//        $add = Address::with('city','state','country')->find(1);
//        $add = Address::with('city')->find(1);
        $user = User::find(1);
        $queries = DB::getQueryLog();
        $statusID = Address::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
        $response = $user->addresses()->where('status_id', '=', $statusID)->select(['id', 'title'])->get();
        dd($response);
//        $status = Status::where('name', '=', 'فعال')->where('model','=',File::class)->first();
//        $user = User::find(1);
//        $role = Role::find(1);
//        $permissions = Permission::all('id');

//        foreach ($permissions as $permission) {
//            $role->permissions()->attach($permissions);
//        }

//        $permissions = Permission::with('moduleCategory')->get();
//        foreach ($permissions as $permission) {
//            $a[$permission->moduleCategory->name][] = ['label' => $permission->name, 'value' => $permission->id];
//        }
//        foreach ($user->permissions as $permission) {
//            $b[] = $permission->id;
//        }
//        dd($a,$b);
//        $person = Person::find(1);
//        dd($person);
//        dd($user->permissions()[0]->moduleCategory);
//        $permissions=$user->permissions()->where('permission_type_id', '=', 1)->with('moduleCategory')->get();
//        foreach ($permissions as $permission) {
//            dd($permissions);
//            $a[$permission->moduleCategory->name][]=['name' => $permission->name,
//            'slug' => $permission->slug];
//        }
//        dd($a);
//        dd($user->person->personable);

//        $files = $user->files()->with(['statuses' => function ($query) {
//            // Filter statuses to get the latest "active" status for each file
//            $query->latest('created_date')->where('name', 'فعال');
//        }])->get();

//        $files = $user->files()->whereHas('currentStatus', function ($query) {
//            $query->where('name', 'فعال');
//        })->get();
//        $files = $user->files()->whereHas('statuses', function ($query) {
//            $query->where('name', 'فعال')->latest('created_date');
//        })->get();
//        dd($files);
        //        dd($status);
//        dd(File::class);
//        dd(File::GetAllStatuses());
//        $permissionTypesData = json_decode(file_get_contents(realpath(__DIR__.'/../../../modules_statuses.json')), true);
//        dd($permissionTypesData);
//        $a = DB::table('permission_types')->where('name', '=', 'sidebar')->get('id')->first();

//        dd(json_decode(file_get_contents(realpath(__DIR__.'/fileTypes.json')), true));

    }
}
