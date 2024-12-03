<?php
namespace Modules\OUnitMS\Database\Seeders;
use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\Job;
use Modules\OUnitMS\app\Models\OrganizationUnit;

class OUinitStatusSeeder extends Seeder
{
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/OUinitStatusSeeder.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => OrganizationUnit::class,
            ]);
        }

    }
}
