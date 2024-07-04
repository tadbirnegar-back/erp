<?php

namespace Modules\HRMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\IssueTime;

class IssueTimesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);
        $issueTimes = json_decode(file_get_contents(realpath(__DIR__.'/issueTimes.json')), true);

        IssueTime::insert($issueTimes);
    }
}
