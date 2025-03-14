<?php

namespace Modules\ACC\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\ACC\app\Http\Enums\AccountCategoryTypeEnum;

class AccountCategoryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = AccountCategoryTypeEnum::getAllLabelAndValues()->map(function ($item) {
            return [
                'id' => $item['value'],
                'name' => $item['label'],
            ];
        })->toArray();
        DB::table('acc_account_category_types')->upsert($data, ['id']);
    }
}
