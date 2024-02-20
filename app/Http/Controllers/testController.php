<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\ModuleCategory;
use Modules\AAA\app\Models\Permission;
use Modules\AAA\app\Models\Role;
use Modules\AAA\app\Models\User;
use Modules\AddressMS\app\Models\Address;
use Modules\BranchMS\app\Models\Branch;
use Modules\CustomerMS\app\Http\Repositories\CustomerRepository;
use Modules\FileMS\app\Models\File;
use Modules\PersonMS\app\Http\Repositories\PersonRepository;
use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;

class testController extends Controller
{
    public function run(CustomerRepository $personRepository): void
    {
//        $role = Role::find(2);

//        $role = Role::with(['permissions.moduleCategory','status','section.department.branch'])->find(1);
//
//        $permissionsGroupedByCategory = $role->permissions
//            ->groupBy('moduleCategory.name');
//        dd($role->status,$role->section,$permissionsGroupedByCategory);
//        $result = User::find(1);


//        $result = Permission::with('moduleCategory')->get();
//        $result = ModuleCategory::with('permissions')->groupBy('name','id','icon')->get();
//        $result = ModuleCategory::with(['modules.permissions' => function ($query) {
//            $query->where('slug', 'not like', '%update%');
//        }])->whereHas('modules.permissions', function ($query) {
//            $query->where('slug', 'not like', '%update%');
//        })->get();

//        $result = ModuleCategory::select('name')
//            ->withCount('permissions') // For counting related permissions
//            ->groupBy('name')
//            ->get();
//        $result = ModuleCategory::with('permissions')->groupBy('name', 'id')->get();

//        $result = $personRepository->show(7);
//        $result = Person::with('personable', 'avatar', 'status')->where('national_code', '=', '2840127121')->first();
//        $result = Person::where('national_code', '=', '2840127121')->first();
//        dd($result->hasPermissionForRoute('/address/{id}'));
//        $filesWithActiveStatus = Branch::whereHas('status', function ($query) {
//            $query->where('name', 'فعال')
//                ->where('branch_status.create_date', function($subQuery) {
//                    $subQuery->selectRaw('MAX(create_date)')
//                        ->from('branch_status')
//                        ->whereColumn('branch_id', 'branches.id');
//                });
//        })->get();
//        dd($filesWithActiveStatus);
////        DB::enableQueryLog();
//
//        $a = Natural::find(15);
//      $b=  $a->profilePicture;
//        $queries = DB::getQueryLog();
//
//        dd($b,$queries);

//        $add = Address::with('city.state.country')->find(1);
//        $add = Address::with('city','state','country')->find(1);
//        $add = Address::with('city')->find(1);
//        $user = User::find(1);
////        $statusID = Address::find(1);
//        $permissions=$user->permissions()->where('permission_type_id', '=', 1)->with('moduleCategory')->get();
//        foreach ($permissions as $permission) {
//            $sidebarItems[$permission->moduleCategory->name]['subPermission'][]=[
//                'label' => $permission->name,
//                'slug' => $permission->slug,
//            ];
//            $sidebarItems[$permission->moduleCategory->name]['icon'] = $permission->moduleCategory->icon;
//        }
//        dd($permissions);
//        $status = Status::where('name', '=', 'فعال')->where('model','=',File::class)->first();
//        $user = User::find(1);
//        $role = Role::find(1);
//        $permissions = Permission::all('id');
//
//            $role->permissions()->sync($permissions);
//
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
