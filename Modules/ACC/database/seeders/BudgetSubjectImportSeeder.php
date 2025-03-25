<?php

namespace Modules\ACC\database\seeders;

use Illuminate\Database\Seeder;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACMS\app\Http\Enums\SubjectTypeEnum;
use Modules\ACMS\app\Http\Trait\CircularSubjectsTrait;
use Spatie\SimpleExcel\SimpleExcelReader;

class BudgetSubjectImportSeeder extends Seeder
{
    use CircularSubjectsTrait, AccountTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $incomeXlsxPath = realpath(__DIR__ . '/income.xlsx');
            $incomeExcel = SimpleExcelReader::create($incomeXlsxPath)->getRows();

            $expanseXlsxPath = realpath(__DIR__ . '/Expanse.xlsx');
            $expanseExcel = SimpleExcelReader::create($expanseXlsxPath)->getRows();

            $omraniXlsxPath = realpath(__DIR__ . '/omrani.xlsx');
            $omraniExcel = SimpleExcelReader::create($omraniXlsxPath)->getRows();


            $incomeExcel->each(function ($row) {
                $income1Data = [
                    'subjectName' => $row['name_1'],
                    'code' => $row['code_1'],
                    'subjectTypeID' => SubjectTypeEnum::INCOME->value,
                ];

                $income1 = $this->storeSubject($income1Data);
                // ==========================================================================

                $income2Data = [
                    'subjectName' => $row['name_2'],
                    'code' => $row['code_2'],
                    'subjectTypeID' => SubjectTypeEnum::INCOME->value,
                    'parentID' => $income1->id,
                ];

                $income2 = $this->storeSubject($income2Data);
                // ==========================================================================

                $income3Data = [
                    'subjectName' => $row['name_3'],
                    'code' => $row['code_3'],
                    'subjectTypeID' => SubjectTypeEnum::INCOME->value,
                    'parentID' => $income2->id,
                ];

                $income3 = $this->storeSubject($income3Data);
                // ==========================================================================
                $income4Data = [
                    'subjectName' => $row['name_4'],
                    'code' => $row['code_4'],
                    'subjectTypeID' => SubjectTypeEnum::INCOME->value,
                    'parentID' => $income3->id,
                ];

                $income4 = $this->storeSubject($income4Data);
                // ==========================================================================


            });


        } catch (\Exception $e) {

        }
    }
}
