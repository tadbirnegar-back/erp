<?php

namespace App\Http\Controllers;


use DB;
use Modules\ACC\app\Http\Enums\DocumentStatusEnum;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Http\Traits\ArticleTrait;
use Modules\ACC\app\Http\Traits\DocumentTrait;
use Modules\ACC\app\Models\Document;
use Modules\ACMS\app\Http\Trait\CircularSubjectsTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\BNK\app\Http\Traits\BankTrait;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Http\Traits\TransactionTrait;
use Morilog\Jalali\Jalalian;

class testController extends Controller
{
    use BankTrait, ChequeTrait, TransactionTrait, FiscalYearTrait, DocumentTrait, AccountTrait, ArticleTrait, CircularSubjectsTrait;

    /**
     * Execute the job.
     */
    public function run(): void
    {
        DB::enableQueryLog();
        for ($i = 0; $i < 9; $i++) {
            $this->inactiveAccountStatus();
        }
        $query = DB::getQueryLog();


        dump($query);


        echo "<html><head></head><body></body></html>";
    }
}
