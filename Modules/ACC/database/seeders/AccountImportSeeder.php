<?php

namespace Modules\ACC\database\seeders;

use Illuminate\Database\Seeder;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Models\AccountCategory;
use Spatie\SimpleExcel\SimpleExcelReader;

class AccountImportSeeder extends Seeder
{
    use AccountTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pathToXlsx = realpath(__DIR__ . '/Samad Accounts List STD fixed.xlsx');

        $rows = SimpleExcelReader::create($pathToXlsx)
            ->getRows();
        \DB::beginTransaction();
        $rows->each(function ($row) {
            $cat = AccountCategory::where('name', $row['cat_name'])
                ->where('id', $row['cat_id'])
                ->first();

            $dataKol = [
                'name' => $row['kol_name'],
                'categoryID' => $cat->id,
                'ounitID' => null,
                'segmentCode' => $row['segment_id'] ?? null,
                'chainCode' => $row['kol_id'],
            ];

            $kolAccount = $this->firstOrStoreAccount($dataKol, null, 1);

            $dataMoein = [
                'name' => $row['moein_name'],
                'categoryID' => $cat->id,
                'ounitID' => null,
                'segmentCode' => $row['m_segment_id'] ?? null,
                'chainCode' => $row['moein_id'],
            ];

            $moeinAccount = $this->firstOrStoreAccount($dataMoein, $kolAccount, 1);


        });
        \DB::commit();

    }
}
