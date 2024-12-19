<?php

namespace Modules\PayStream\database\seeders;

use Illuminate\Database\Seeder;
use Modules\PayStream\database\seeders\ProcessStatusSeeder;
use Modules\PayStream\app\Models\FinancialStatus;

class PayStreamDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
//            FinancialStatusSeeder::class,
//            ProcessStatusSeeder::class,
//            InvoiceStatusSeeder::class,
            PsPaymentStatusSeeder::class,
        ]);
    }
}
