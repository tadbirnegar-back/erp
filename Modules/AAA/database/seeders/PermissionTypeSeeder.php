<?php

namespace Modules\AAA\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionTypesData = json_decode(file_get_contents(realpath(__DIR__.'/permissionTypes.json')), true);
        foreach ($permissionTypesData as $permissionType) {
            DB::table('permission_types')->insertGetId([
//                'id'=>$permissionType['id'],
                'name' => $permissionType['name'],
            ]);
        }
    }
}
