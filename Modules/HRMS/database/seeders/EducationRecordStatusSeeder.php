<?php

namespace Modules\HRMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Http\Enums\EducationalRecordStatusEnum;
use Modules\HRMS\app\Models\EducationalRecord;
use Modules\StatusMS\app\Models\Status;

class EducationRecordStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plStatuses = collect(EducationalRecordStatusEnum::cases());

        $plStatuses->each(function ($plStatus) {
            Status::insertOrIgnore([
                'name' => $plStatus->value,
                'model' => EducationalRecord::class,
            ]);
        });
        // $this->call([]);
    }
}
