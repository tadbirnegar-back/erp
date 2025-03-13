<?php

namespace App\Http\Controllers;


use DB;
use Modules\ACC\app\Http\Enums\AccCategoryEnum;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Http\Traits\ArticleTrait;
use Modules\ACC\app\Http\Traits\DocumentTrait;
use Modules\ACC\app\Models\Document;
use Modules\ACMS\app\Http\Trait\CircularSubjectsTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\BNK\app\Http\Traits\BankTrait;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Http\Traits\TransactionTrait;
use Modules\EMS\app\Http\Traits\EnactmentReviewTrait;
use Modules\EMS\app\Http\Traits\EnactmentTrait;

class testController extends Controller
{
    use BankTrait, ChequeTrait, TransactionTrait, FiscalYearTrait, DocumentTrait, AccountTrait, ArticleTrait, CircularSubjectsTrait, EnactmentReviewTrait, EnactmentTrait;

    private int $ounitID;
    private int $fileID;

    public function __construct()
    {
        $this->ounitID = 5;
        $this->fileID = 5823;
    }

    public function run()
    {
        $docs = Document::joinRelationship('articles.account')
            ->where('acc_documents.ounit_id', 5)->whereIntegerNotInRaw('category_id', [AccCategoryEnum::INCOME->value, AccCategoryEnum::EXPENSE->value])
            ->select(
                [
                    'acc_accounts.id',
                    'acc_accounts.name',
                    'acc_accounts.chain_code',
                    'acc_accounts.new_chain_code',
                    'acc_accounts.parent_id',
                    'acc_accounts.category_id',

                    DB::raw('SUM(acc_articles.credit_amount) - SUM(acc_articles.debt_amount) AS total'),
                ]
            )
            ->groupBy(
                'acc_accounts.id',
                'acc_accounts.name',
                'acc_accounts.chain_code',
                'acc_accounts.new_chain_code',
                'acc_accounts.parent_id',
                'acc_accounts.category_id',


            )
            ->having('total', '!=', 0)
            ->get();
        dump($docs);
        // ImportDocsJob::dispatch($this->ounitID, $this->fileID);

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
