<?php

namespace Modules\ProductMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\ProductMS\app\Models\VariantGroup;

class VariantGroupStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/variantGroupStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => VariantGroup::class,
            ]);
        }
        // $this->call([]);
    }
}
