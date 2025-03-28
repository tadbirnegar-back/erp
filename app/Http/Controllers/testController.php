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
//        $a = Article::joinRelationship('document', function ($join) {
//            $join->where('ounit_id', '=', '839')
//                ->where('fiscal_year_id', '=', '1');
//
//        })
//            ->where('account_id', '=', '2605')
//            ->select([
//                DB::raw('SUM(credit_amount) - SUM(debt_amount) as remaining'),
//            ])
//            ->first();
        $lastDocNumber = Document::where('fiscal_year_id', 1)
            ->where('ounit_id', 5)
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value);
            }])
            ->where('description','!=','سند تبدیل سرفصل حساب ها به کدینگ جدید')
            ->orderByRaw('CAST(document_number AS UNSIGNED) DESC')
            ->first();

        $date = $lastDocNumber->getRawOriginal('document_date');
        $a=Jalalian::fromDateTime($date)->toDateString();
        dd(convertJalaliPersianCharactersToGregorian($a));

//        $user = User::where('mobile', '9374281717')->first();
//        $recruitmentScripts = $user
//            ->activeRecruitmentScripts()
//            ->whereHas('scriptType', function ($query) {
//                $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
//            })->get();
//
//        foreach ($recruitmentScripts->pluck('organization_unit_id')->toArray() as $item) {
//            echo $item . ',';
//        }


        //with recursive `laravel_cte` as ((select `acc_accounts`.*, 0 as `depth`, cast(`id` as char(65535)) as `path` from `acc_accounts` where `acc_accounts`.`id` in (1)) union all (select `acc_accounts`.*, `depth` + 1 as `depth`, concat(`path`, ?, `acc_accounts`.`id`) from `acc_accounts` inner join `laravel_cte` on `laravel_cte`.`id` = `acc_accounts`.`parent_id`)) select * from `laravel_cte`

//        $a = Account::where('accountable_type', SubAccount::class)->isLeaf()->get();
//        $b = array(
//            "صندوق",
//            "حسابهای دریافتنی تجاری",
//            "چکهای دریافتنی نزد صندوق",
//            "چکهای در جریان وصول",
//            "اسناد واخواست شده",
//            "اسناد پرداختنی",
//            "حق الزحمه پرداختنی",
//            "ذخیره مرخصی استفاده‌ نشده",
//            "ذخیره مزایای پایان خدمت کارکنان");
//        $a->each(function ($item) use ($b) {
//            $name = trim($item->name);
//
//            if (in_array($name, array_map('trim', $b))) {
//                $item->isFertile = true;
//            } else {
//                $item->isFertile = false;
//            }
//            $item->save();
//
//        });
//        dd($a->pluck('isFertile'));
        $results = DB::table(DB::raw('(
                WITH RECURSIVE descendants AS (
                    SELECT id, id as root_id
                    FROM acc_accounts
                    WHERE id ="' . 1 . '"
                    UNION ALL
                    SELECT a.id, d.root_id
                    FROM acc_accounts a
                    INNER JOIN descendants d ON a.parent_id = d.id
                    WHERE (a.ounit_id = ' . 5 . ' OR a.ounit_id IS NULL)
                        )
                SELECT * FROM descendants
            ) as descendants'))
            ->join('acc_articles', 'acc_articles.account_id', '=', 'descendants.id')
            ->join('acc_documents', 'acc_documents.id', '=', 'acc_articles.document_id')
            // Join the pivot table for statuses. This table contains the create_date and status_name.
            ->join('accDocument_status', 'accDocument_status.document_id', '=', 'acc_documents.id')
            ->join('statuses', 'accDocument_status.status_id', '=', 'statuses.id')
            ->join('acc_accounts as root_account', 'root_account.id', '=', 'descendants.root_id')
            // Ensure we only get the latest status per document
            ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
            // And only if that latest status has the name "active"
            ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value)
            ->where('acc_documents.ounit_id', 5)
            ->where('acc_documents.fiscal_year_id', 1)
            ->orderBy('acc_documents.document_date', 'asc')
            ->select(
                [
                    'descendants.root_id',
                    'root_account.name',
                    'root_account.chain_code',
                    'acc_articles.credit_amount',
                    'acc_articles.debt_amount',
                    'acc_articles.description',
                    'acc_documents.document_number',

                ]
            )
            ->get();
//
////        $results = DB::select('with recursive `laravel_cte` as ((select `acc_accounts`.*, 0 as `depth`, cast(`id` as char(65535)) as `path` from `acc_accounts` where `acc_accounts`.`id` in (1)) union all (select `acc_accounts`.*, `depth` + 1 as `depth`, concat(`path`, \'.\', `acc_accounts`.`id`) from `acc_accounts` inner join `laravel_cte` on `laravel_cte`.`id` = `acc_accounts`.`parent_id`)) select * from `laravel_cte` ');
///
///
        $credit = 0;
        $debt = 0;

        $results->each(function ($row) use (&$credit, &$debt) {
            $credit += $row->credit_amount;
            $debt += $row->debt_amount;
            $row->remaining = (int)abs($credit - $debt);

        });
        dd($results);


//        DB::enableQueryLog();
//        $acc = Account::with('descendantsAndSelf')->where('acc_accounts.id', 1)->first();
//        dd($acc, DB::getQueryLog());
//        try {
//            $rows = [
//                [
//                    'cat_name' => 'دارائیهای غیر جاری',
//                    'cat_id' => '2',
//                    'kol_name' => 'دارایی های ثابت مشهود',
//                    'kol_id' => '210',
//                    'segment_id' => '10',
////                    'moein_name' => 'پیش دریافت حقوق و دستمزد پرداختنی سایر کارکنان',
////                    'new_chain_moein' => '31133',
////                    'm_segment_id' => '33',
//                ], [
//                    'cat_name' => 'دارائیهای غیر جاری',
//                    'cat_id' => '2',
//                    'kol_name' => 'استهلاک انباشته',
//                    'kol_id' => '211',
//                    'segment_id' => '11',
////                    'moein_name' => 'پیش دریافت حقوق و دستمزد پرداختنی سایر کارکنان',
////                    'new_chain_moein' => '31133',
////                    'm_segment_id' => '33',
//                ], [
//                    'cat_name' => 'دارائیهای غیر جاری',
//                    'cat_id' => '2',
//                    'kol_name' => 'داراییهای ثابت نامشهود',
//                    'kol_id' => '212',
//                    'segment_id' => '12',
//                    'moein_name' => 'نرم افزارها',
//                    'new_chain_moein' => '21201',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => ' نرم افزار حسابداری چکاد',
//                    'new_chain_tafzil' => '21201001',
//                    'taf_segment_id' => '001',
//                ],
//
//            ];
//            \DB::beginTransaction();
//            $cat = AccountCategory::where('name', $row['cat_name'])
//                ->where('id', $row['cat_id'])
//                ->first();
//
//            $dataKol = [
//                'name' => $row['kol_name'],
//                'categoryID' => $cat->id,
//                'ounitID' => null,
//                'segmentCode' => $row['segment_id'] ?? null,
//                'chainCode' => $row['kol_id'],
//            ];
//
//            $kolAccount = $this->firstOrStoreAccount($dataKol, null, 1);
//
//            $dataMoein = [
//                'name' => $row['moein_name'],
//                'categoryID' => $cat->id,
//                'ounitID' => null,
//                'segmentCode' => $row['m_segment_id'] ?? null,
//                'chainCode' => $row['new_chain_moein'],
//            ];
//
//            $moeinAccount = $this->firstOrStoreAccount($dataMoein, $kolAccount, 1);
//
//            $dataTafzil = [
//                'name' => $row['tafzil_name'],
//                'categoryID' => $cat->id,
//                'ounitID' => 2333,
//                'segmentCode' => $row['m_segment_id'] ?? null,
//                'chainCode' => $row['new_chain_moein'],
//            ];
//
//            $moeinAccount = $this->firstOrStoreAccount($dataMoein, $kolAccount, 1);
//            \DB::commit();
//            dd($moeinAccount);
//        } catch (\Exception $e) {
//            DB::rollBack();
//            dd($e->getMessage());
//        }
    }
}
