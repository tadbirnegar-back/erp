<?php

namespace Modules\FormGMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\FormGMS\app\Models\Field;

class FieldStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/fieldStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => Field::class,
            ]);
        }
        // $this->call([]);
    }
}
