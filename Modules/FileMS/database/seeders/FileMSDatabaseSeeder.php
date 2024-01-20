<?php

namespace Modules\FileMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class FileMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ModuleCategoriesSeeder::class,
            ModuleSeeder::class,
            PermissionsSeedSeeder::class,
            ]);

        $fileTypes = json_decode(file_get_contents(realpath(__DIR__ . '/fileTypes.json')), true);

        foreach ($fileTypes as $key => $fileType) {

            $mimeTypeID = DB::table('mime_types')->insertGetId([
                'name' => $key,
            ]);

            foreach ($fileType as $extName => $fullExt) {
                DB::table('extensions')->insertGetId([
                    'name' => $extName,
                    'type_id' => $mimeTypeID,
                ]);
            }
        }


    }
}
