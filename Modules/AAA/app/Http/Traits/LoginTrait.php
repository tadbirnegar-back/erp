<?php

namespace Modules\AAA\app\Http\Traits;


use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\VCM\app\Models\VcmVersions;

trait LoginTrait
{
    public function takePermissions($user)
    {
//
        return User::query()
            ->joinRelationship('roles.permissions.modules.category')
            ->select([
                'roles.id as role_id',
                'permissions.id  as permission_id',
                'permissions.name as permission_name',
                'permissions.slug as permission_slug',
                'permissions.priority as permission_priority',
                'modules.icon as module_icon',
                'modules.id as module_id',
                'modules.name as module_name',
                'modules.priority as module_priority',
                'module_categories.name as module_cat_name',
                'module_categories.icon as module_cat_icon',
                'module_categories.priority as module_cat_priority',
                'module_categories.id as module_cat_id',
            ])
            ->where('users.id' , $user->id)
            ->where('permissions.permission_type_id', PermissionTypesEnum::SIDEBAR->value)
            ->distinct('roles.id')
            ->get()
            ->groupBy('module_cat_name')
            ->map(function ($modules, $categoryName) {
                return [
                    'category_name' => $categoryName,
                    'category_icon' => $modules->first()->module_cat_icon,
                    'category_id' => $modules->first()->module_cat_id,
                    'modules' => $modules->groupBy('module_name')
                        ->map(function ($permissions, $moduleName) {
                            return [
                                'module_name' => $moduleName,
                                'module_icon' => $permissions->first()->module_icon,
                                'module_id' => $permissions->first()->module_id,
                                'permissions' => $permissions->map(function ($permission) {
                                    return [
                                        'id' => $permission->permission_id,
                                        'name' => $permission->permission_name,
                                        'slug' => $permission->permission_slug,
                                        'priority' => $permission->permission_priority,
                                    ];
                                })->sortBy('priority')->values(),
                            ];
                        })->sortBy('module_priority')->values(),
                ];
            })->sortBy('module_cat_priority')->values();
    }

    public function takeUserInfo($user)
    {
        $user->load(['person' => function ($query) {
            $query->with('personable');
            $query->with('avatar');
        }]);
        return [
            'firstName' => $user->person->personable->first_name,
            'lastName' => $user->person->personable->last_name,
            'avatar' => !is_null($user->person->avatar) ? $user->person->avatar->slug : null,
            'roles' => $user->roles,
        ];
    }

    public function takeApplicationVersion()
    {
        $version = VcmVersions::orderBy('id', 'desc')->first();

        if(is_null($version)){
            return '1.0.0';
        }else{
            return $version->high_version.'.'.$version->mid_version.'.'.$version->low_version;
        }
    }
}
