<?php

namespace Modules\HRMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Http\Traits\JobTrait;
use Modules\HRMS\app\Models\Job;

class FreeZoneJobsSeeder extends Seeder
{
    use JobTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = json_decode(file_get_contents(realpath(__DIR__.'/FreeZoneJobsSeeder.json')), true);
        $status_id = $this->activeJobStatus()->id;
        foreach ($jobs as $job) {
            Job::create([
                "title" => $job['title'],
                "description" => $job['description'],
                "status_id" => $status_id,
            ]);
        }
    }

}
