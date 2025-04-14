<?php

namespace Modules\BNK\database\seeders;

use Illuminate\Database\Seeder;

class BNKDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
//            ModuleCategorySeeder::class,
//            ModuleSeeder::class,
            PermissionsSeeder::class,
//            BankAccountStatusSeeder::class,
//            ChequeStatusSeeder::class,
            ChequeBookStatusSeeder::class,
//            CardStatusSeeder::class,
//            TransactionStatusSeeder::class,
        ]);
    }
}
