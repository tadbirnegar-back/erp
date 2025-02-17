<?php

namespace App\Http\Controllers;


use Modules\BNK\app\Http\Traits\BankTrait;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Http\Traits\TransactionTrait;
use Spatie\SimpleExcel\SimpleExcelReader;


class testController extends Controller
{
    use BankTrait, ChequeTrait, TransactionTrait;

    public function run()
    {

        $pathToXlsx = storage_path('app/public/Updated_Processed_آبگرم.xlsx');
        $rows = SimpleExcelReader::create($pathToXlsx)
            ->getRows();
        $a = $rows->groupBy('year')
            ->map(function ($yearGroup) {
                return $yearGroup->groupBy('Doc ID');
            });
        $a->each(function ($row) {
            dd($row);

        });

        $output = "<!DOCTYPE html>
    <html>
    <head>
        <title>Test Debugbar</title>
    </head>
    <body>
    </body></html>";


        echo $output;

    }
}
