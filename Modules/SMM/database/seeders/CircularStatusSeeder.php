<?php

namespace Modules\SMM\database\seeders;

use Illuminate\Database\Seeder;
use Modules\SMM\app\Enums\CircularStatusEnum;
use Modules\SMM\app\Models\Circular;
use Modules\StatusMS\app\Models\Status;

class CircularStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = collect(CircularStatusEnum::cases());

        $statuses->each(function ($status) {
            Status::insertOrIgnore([
                'name' => $status->value,
                'model' => Circular::class,
                'class_name' => $status->getClassName(),
            ]);
        });
    }
}
