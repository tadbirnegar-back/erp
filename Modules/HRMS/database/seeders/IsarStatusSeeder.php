<?php

namespace Modules\HRMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Http\Enums\IsarStatusEnum;
use Modules\HRMS\app\Models\Isar;
use Modules\StatusMS\app\Models\Status;

class IsarStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plStatuses = collect(IsarStatusEnum::cases());

        $plStatuses->each(function ($plStatus) {
            Status::insertOrIgnore([
                'name' => $plStatus->value,
                'model' => Isar::class,
            ]);
        });
    }
}
