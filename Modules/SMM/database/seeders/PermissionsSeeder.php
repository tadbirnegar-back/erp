<?php

namespace Modules\SMM\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = json_decode(file_get_contents(realpath(__DIR__ . '/permissions.json')), true);

        foreach ($permissions as $permission) {
            $module = \DB::table('modules')->where('name', '=', $permission['moduleName'])->get('id')->first();

            $permissionType = \DB::table('permission_types')->where('name', '=', $permission['permissionTypeName'])->get('id')->first();

            DB::table('permissions')->updateOrInsert([
                'name' => $permission['name'],
                'slug' => $permission['slug'],
                'module_id' => $module->id,
                'permission_type_id' => $permissionType->id,
            ]);
        }
    }
}
