<?php

namespace Modules\ACC\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\ACC\app\Http\Enums\AccCategoryEnum;

class AccountCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = AccCategoryEnum::getAllLabelAndValues()->map(function ($item) {
            return [
                'id' => $item['value'],
                'name' => $item['label'],
                'account_category_type_id' => AccCategoryEnum::from($item['value'])->getCatTypeEnum()->value,

            ];
        })->toArray();

        DB::table('acc_account_categories')->upsert($data, ['id']);

    }
}
