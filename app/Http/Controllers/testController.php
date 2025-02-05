<?php

namespace App\Http\Controllers;


use Modules\ACC\app\Http\Enums\DocumentStatusEnum;
use Modules\ACC\app\Http\Enums\DocumentTypeEnum;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\Document;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\BNK\app\Http\Enums\ChequeStatusEnum;
use Modules\BNK\app\Http\Traits\BankTrait;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Http\Traits\TransactionTrait;
use Modules\BNK\app\Models\BankAccount;
use Modules\BNK\app\Models\BnkChequeStatus;
use Modules\BNK\app\Models\Cheque;


class testController extends Controller
{
    use BankTrait, ChequeTrait, TransactionTrait;

    public function run()
    {
        $testData = [
            ['id' => 1, 'title' => 'Article 1', 'paymentType' => 1],
            ['id' => 2, 'title' => 'Article 2', 'paymentType' => 2],
            ['id' => 3, 'title' => 'Article 3'], // No paymentType key
            ['id' => 4, 'title' => 'Article 4', 'paymentType' => 1],
            ['id' => 5, 'title' => 'Article 5', 'paymentType' => 2],
            ['id' => 6, 'title' => 'Article 6'], // No paymentType key
            ['id' => 7, 'title' => 'Article 7', 'paymentType' => 1],
            ['id' => 8, 'title' => 'Article 8', 'paymentType' => 2],
        ];

        $data['articles'] = json_encode($testData);

        $articles = json_decode($data['articles'], true);

        $cheques = array_filter($articles, fn($article) => isset($article['paymentType']) && $article['paymentType'] == 2 && !isset($article['transactionID']));

        foreach ($cheques as $cheque) {
            //chequeID
            //payeeName
            //transactionCode
            //dueDate
            //paymentType
            $whiteCheque = Cheque::with('chequeBook')->find($cheque['chequeID']);
            $whiteCheque = $this->updateCheque($cheque, $whiteCheque);
            $whiteCheque->statuses()->attach($this->issuedChequeStatus()->id);
            $cheque['withdrawal'] = $cheque['creditAmount'];
            $cheque['bankAccountID'] = 11;
            $cheque['userID'] = 1905;
            $cheque['chequeID'] = $whiteCheque->id;
            $transaction = $this->storeTransaction($data);

        }


//        $internetBanks = array_filter($articles, fn($article) => isset($article['paymentType']) && $article['paymentType'] == 2);
//        $noPaymentType = array_filter($articles, fn($article) => !isset($article['paymentType']));
//
//        dump($cheques);
//        dump($internetBanks);
//        dump($noPaymentType);
        dd(!empty($cheques));
        $account = Account::with(['cheques'])->find(69);
        $cheque = Cheque::
        joinRelationship('chequeBook.account', [
            'account' => function ($join) {
                $join
                    ->where(Account::getTableName() . '.id', 200)
                    ->where(Account::getTableName() . '.entity_type', BankAccount::class);
            }
        ])
            ->joinRelationship('statuses', [
                'statuses' => function ($join) {
                    $join
                        ->whereRaw(BnkChequeStatus::getTableName() . '.create_date = (SELECT MAX(create_date) FROM ' . BnkChequeStatus::getTableName() . ' WHERE cheque_id = bnk_cheques.id)')
                        ->where('statuses.name', '=', ChequeStatusEnum::BLANK->value);
                }
            ])
            ->orderBy('segment_number', 'asc')
            ->first();
        dd($cheque);
//        $accs = AccountCategory::leftJoinRelationship('accounts', function ($join) {
//            $join
//                ->where(function ($query) {
//                    $query->where('acc_accounts.ounit_id', 197)
//                        ->orWhereNull('acc_accounts.ounit_id');
//                })
//                ->withGlobalScopes();
//        })
//            ->select([
//                'acc_accounts.id as id',
//                'acc_accounts.name as title',
//                'acc_accounts.segment_code as code',
//                'acc_accounts.chain_code as chainedCode',
//                'acc_accounts.parent_id as parent_id',
//                'acc_account_categories.id as categoryID',
//                'acc_account_categories.name as accountCategory',
//            ])
//            ->get();
//        $a = $accs->groupBy('accountCategory')->map(function ($item) {
//            return $item->toHierarchy();
//        });
//        return response()->json(['data' => $a]);

        $data = [
            'ounitID' => 197,
            'fiscalYearID' => 2
        ];
        $fiscalYear = FiscalYear::find($data['fiscalYearID']);
        $lastYearFiscalYear = FiscalYear::where('name', $fiscalYear->name - 1)->first();

        $lastYearClosingDoc = Document::joinRelationship('articles.account')
            ->where('acc_documents.ounit_id', $data['ounitID'])
            ->where('acc_documents.document_type_id', DocumentTypeEnum::CLOSING->value)
            ->where('acc_documents.fiscal_year_id', $lastYearFiscalYear->id)
            ->joinRelationship('statuses', [
                'statuses' => function ($join) {
                    $join
                        ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                        ->where('statuses.name', '=', DocumentStatusEnum::CONFIRMED->value);
                }
            ])
//            ->withoutGlobalScopes()
            ->select([
                \DB::raw('SUM( acc_articles.debt_amount) as total_debt_amount'),
                \DB::raw('SUM( acc_articles.credit_amount) as total_credit_amount'),
                'acc_accounts.id as id',
                'acc_accounts.name as name',
                'acc_accounts.segment_code as code',
                'acc_accounts.chain_code as chainedCode',

            ])
            ->groupBy(
                'acc_accounts.id',
                'acc_accounts.name',
                'acc_accounts.segment_code',
                'acc_accounts.chain_code'
            )
            ->get();

        dd($lastYearClosingDoc);

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
