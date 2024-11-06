<?php

namespace Modules\EMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = json_decode(file_get_contents(realpath(__DIR__ . '/permissions.json')), true);

        foreach ($permissions as $permission) {
            $module = DB::table('modules')
                ->where('name', '=', $permission['moduleName'])
                ->get('id')
                ->first();
            $permissionType = DB::table('permission_types')
                ->where('name', '=', $permission['permissionTypeName'])
                ->get('id')
                ->first();

            if ($module && $permissionType) {
                DB::table('permissions')->updateOrInsert(
                    ['slug' => $permission['slug'], 'module_id' => $module->id], // Condition for matching an existing record
                    [
                        'name' => $permission['name'], // Fields to update or insert
                        'permission_type_id' => $permissionType->id,
                    ]
                );
            }
        }
    }

}
