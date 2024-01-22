<?php

namespace Modules\FileMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\FileMS\app\Models\File;

class FileStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/fileStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => File::class,
            ]);
            // $this->call([]);
        }
    }
}
