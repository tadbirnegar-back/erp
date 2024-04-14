<?php

namespace Modules\OUnitMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\OUnitMS\app\Models\OrganizationUnit;

class OrganizationUnitStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/organizationUnitStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => OrganizationUnit::class,
            ]);
        }
        // $this->call([]);
    }
}
