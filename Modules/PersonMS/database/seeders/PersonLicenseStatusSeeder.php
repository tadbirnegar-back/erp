<?php

namespace Modules\PersonMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\PersonMS\app\Http\Enums\PersonLicenseStatusEnum;
use Modules\PersonMS\app\Models\PersonLicense;
use Modules\StatusMS\app\Models\Status;

class PersonLicenseStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plStatuses = collect(PersonLicenseStatusEnum::cases());

        $plStatuses->each(function ($plStatus) {
            Status::insertOrIgnore([
                'name' => $plStatus->value,
                'model' => PersonLicense::class,
            ]);
        });
    }
}
