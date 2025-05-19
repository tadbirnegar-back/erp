<?php

namespace Modules\PersonMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PersonMS\app\Http\Enums\PersonStatusEnum;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;

class PersonStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plStatuses = collect(PersonStatusEnum::cases());

        $plStatuses->each(function ($plStatus) {
            Status::firstOrCreate([
                'name' => $plStatus->value,
                'model' => Person::class,
                'class_name' => $plStatus->getClassName(),
            ]);
        });
    }
}
