<?php

namespace Modules\PayStream\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Lesson;
use Modules\PayStream\app\Models\FinancialStatus;
use Modules\PayStream\app\Models\Invoice;
use Modules\PayStream\app\Models\Order;
use Modules\PayStream\app\Models\ProcessStatus;
use Modules\PayStream\app\Models\PsPayments;

class PsPaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/InvoiceStatusSeeder.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $userStatus['name'],
                'model' => PsPayments::class,
            ], [
                'name' => $userStatus['name'],
                'model' => PsPayments::class,
                'class_name' => $userStatus['className'] ?? null,
            ]);
        }
        // $this->call([]);
    }
}
