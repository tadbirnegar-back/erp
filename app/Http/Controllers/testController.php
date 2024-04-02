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
use Modules\CustomerMS\app\Models\Customer;
use Modules\CustomerMS\app\Models\ShoppingCustomer;
use Modules\FileMS\app\Models\File;
use Modules\FormGMS\app\Models\Field;
use Modules\FormGMS\app\Models\Form;
use Modules\PersonMS\app\Http\Repositories\PersonRepository;
use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\ProductMS\app\Models\Variant;
use Modules\ProductMS\app\Models\VariantGroup;
use Modules\StatusMS\app\Models\Status;
use Modules\WidgetsMS\app\Http\Repositories\WidgetRepository;
use Str;

class testController extends Controller
{


    public function run(): void
    {
        $className = "Modules\AAA\app\Http\widgets\UserWidgets";
        $methodName = "getUserInfo";

// Call the method using call_user_func()
        $result = call_user_func([$className, $methodName],1);

        $user = User::find(1);
//        $sidebarPermissions = $user->permissions()
//            ->whereHas('permissionTypes', function ($query) {
//                $query->where('name', 'widget');
//            })
//            ->get();
        $a = $user->load('activeWidgets.permission');
        dd($user);
        $activeWidgets = $a->activeWidgets;
        $allPermissions = $activeWidgets->map(function ($widget) {
            return $widget->permission->slug; // Extract permission model
        });
//        dd($allPermissions->toArray());
        $b = WidgetRepository::extractor($allPermissions->toArray());
//        dd($b);
        foreach ($b as $key=> $item) {

            $res[$key] = call_user_func([$item['controller'],$item['method']],$user->id);
        }
        dd($res);

//        $routes = \Route::getRoutes()->getRoutes();
////        dd($routes);
//        $targetURI = 'users/roles/list';
//
//        $matchingRoute = null;
//
//        foreach ($routes as $route) {
//            if (str_contains($route->uri(),$targetURI)) {
//                $action = $route->getAction();
//                $matchingRoute = [
//                    'controller' => explode('@', $action['controller'])[0],
//                    'method' => $route->getActionMethod(),
//                ];
//                break; // Exit loop after finding the first matching route
//            }
//        }
//
//        if ($matchingRoute) {
//            echo "Controller: " . $matchingRoute['controller'];
//            echo "<br> Method: " . $matchingRoute['method'];
//        } else {
//            echo "Route 'user/profile' not found.";
//        }

//        DB::enableQueryLog();
//
//        $c = ShoppingCustomer::with(['customer.person.personable'])->findOrFail(9);
//        $queryLog = DB::getQueryLog();
////        $lastQuery = end($queryLog);
//
//// Output the last query executed
//        dd($queryLog);
//
//        dd($c);
//        $b=json_decode($a,true);
//        dd(json_decode($b['variants'], true));
//        $variantGroupId = 123; // Replace 123 with the specific variant group ID
//
//        $variantGroup = VariantGroup::with('status', 'variants.status')
//            ->where('id', $variantGroupId)
//            ->whereHas('status', function ($query) {
//                $query->where('name', 'active');
//            })
//            ->with(['variants' => function ($query) {
//                $query->whereHas('status', function ($query) {
//                    $query->where('name', 'active');
//                });
//            })
//            ->first();
//        $activeVariantGroups = VariantGroup::whereHas('status', function ($statusQuery) {
//            $statusQuery->where('name', 'فعال');
//        })
//            ->orWhereHas('variants', function ($query) {
//                $query->whereHas('status', function ($nestedStatusQuery) {
//                    $nestedStatusQuery->where('name', 'فعال');
//                });
//            })
//            ->get();

//        $activeVariantGroups = VariantGroup::with(['variants' => function ($query) {
//            $query->whereHas('status', function ($statusQuery) {
//                $statusQuery->where('name', 'فعال');
//            });
//        },'status'=>function ($q) {
//            $q->whereHas('status', function ($statusQuery) {
//                $statusQuery->where('name', 'فعال');
//            });
//
//        }])->get();


//        $variantGroups = VariantGroup::whereHas('status', function ($query) {
//            $query->where('name', 'فعال');
//        })->with(['variants' => function ($query) {
//            $query->whereHas('status', function ($query) {
//                $query->where('name', 'فعال');
//            });
//        }])->findOrFail(12);
//        $variantGroups = VariantGroup::
//            whereHas('status', function ($query) {
//                $query->where('name', 'فعال');
//            })
//            ->with(['variants' => function ($query) {
//                $query->whereHas('status', function ($query) {
//                    $query->where('name', 'فعال');
//                });
//            }])
//            ->findOrFail(12);
//        dd($variantGroups);
//        $user = User::find(1);
//        $permissions = $user->permissions()->with(['moduleCategory', 'permissionTypes'])->get();
//
//        dd($permissions->groupBy('permissionTypes.name'));

//        function uniqueCombinations($arrays)
//        {
//
//            $combinations = [];
//
//            // Helper function for recursive generation
//            function generateCombinations($current, $remaining, &$combinations)
//            {
//                if (count($remaining) == 1) {
//                    foreach ($remaining[0] as $element) {
//                        $sortedCombination = array_merge($current, [$element]);
//                        sort($sortedCombination); // Sort in ascending order
//                        $combinations[] = $sortedCombination;
//                    }
//                    return;
//                }
//
//                foreach ($remaining[0] as $i => $element) {
//                    $current[] = $element;
//                    generateCombinations($current, array_slice($remaining, 1), $combinations);
//                    array_pop($current); // Backtrack
//                }
//            }
//
//            generateCombinations([], $arrays, $combinations);
//
//            return array_unique($combinations, SORT_REGULAR); // Ensure unique combinations
//        }
//
//        $data = [
//            [1, 2],
//            [7],
//            [98, 47, 14],
//            [5, 11], // Dynamic number of arrays
//        ];
//
//        $yourArray = uniqueCombinations($data);
//        $c = sort($yourArray);
//        function searchArray($arr, $searchArr) {
//            foreach ($arr as $subArr) {
//                if (is_array($subArr) && count($subArr) === count($searchArr)) { // Check for same length
//                    // Sort both arrays before comparison
//                    $sortedSubArr = $subArr;
//                    sort($sortedSubArr);
//                    $sortedSearchArr = $searchArr;
//                    sort($sortedSearchArr);
//
//
//                    if ($sortedSubArr === $sortedSearchArr) { // Match after sorting
//                        return $subArr;
//                    } else {
//                        $result = searchArray($subArr, $searchArr); // Recursive call
//                        if ($result) {
//                            return $result;
//                        }
//                    }
//                }
//            }
//            return null; // No match found
//        }
//
//        $matchingArray = searchArray($yourArray, [1, 14, 7, 5]);
//        $a = implode(',', $matchingArray);
//        $b['1,7,14,5']=['price'=>250,'combo'=>[1,7,14,5]];
//        dd($yourArray);


//        $user = User::find(11);
//        dd($user->status);
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
//        $sidebarPermissions = $user->permissions()->where('permission_type_id', '=', 1)->with('moduleCategory')->get();
//        foreach ($sidebarPermissions as $permission) {
//            $sidebarItems[$permission->moduleCategory->name]['subPermission'][] = [
//                'label' => $permission->name,
//                'slug' => $permission->slug,
//            ];
//            $sidebarItems[$permission->moduleCategory->name]['icon'] = $permission->moduleCategory->icon;
//        }
//        dd($sidebarPermissions);
//        $role = Role::find(1);
//        $permissions = Permission::all('id');
////
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
