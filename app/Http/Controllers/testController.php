<?php

namespace App\Http\Controllers;


use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Http\Traits\ArticleTrait;
use Modules\ACC\app\Http\Traits\DocumentTrait;
use Modules\ACMS\app\Http\Trait\CircularSubjectsTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\ACMS\app\Models\BudgetItem;
use Modules\ACMS\app\Models\CircularItem;
use Modules\ACMS\app\Models\CircularSubject;
use Modules\BNK\app\Http\Traits\BankTrait;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Http\Traits\TransactionTrait;
use Modules\HRMS\app\Http\Traits\JobTrait;
use Modules\HRMS\app\Http\Traits\LevelTrait;
use Modules\HRMS\app\Http\Traits\PositionTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;

class testController extends Controller
{
    use BankTrait, ChequeTrait, TransactionTrait, FiscalYearTrait, DocumentTrait, AccountTrait, ArticleTrait, CircularSubjectsTrait;
    use JobTrait, PositionTrait, LevelTrait, JobTrait, RecruitmentScriptTrait;

    /**
     * Execute the job.
     */
    function updateDescendants($parent, $children)
    {
        // Optional: update the parent if needed
        // $parent->field = 'newValue';
        // $parent->save();

        foreach ($children as $child) {
            // Update the child


            // Check if the child has its own children
            if ($child->children && $child->children->isNotEmpty()) {
                $this->updateDescendants($child, $child->children);
            }
        }
    }

    public function run()
    {
//        $a =
//        try {
//            DB::beginTransaction();
//            $ba=Budget::whereDoesntHave('budgetItems')->get();
//            dd($ba);
//            $ba->each(function ($item) {
//                $item->delete();
//            });
//            DB::commit();
//            dd('done');
//        }catch (\Exception $e) {
//            DB::rollBack();
//            dd($e->getMessage());
//        }

//dd($ba->pluck('name'));
//        $user = User::with('employee','person')->where('mobile','9142883817')->first();
//        dd($user);
//        $a=Document::where('ounit_id',184)->whereHas('latestStatus',function ($query) {
//            $query->where('name',DocumentStatusEnum::DELETED->value);
//        })->first();
//        dd($a);
//        try {
//            \DB::beginTransaction();
////            $a = array('11150', '11210', '11220', '11230', '12140', '12191', '12192', '12194', '12195', '12196', '12210', '12230', '12140');
//
////            $a = array(12250, 13110, 13170, 13190, 13195, 13196, 13220, 15110, 15120, 21010);
//            $a = array(16240);
////            $a = array('210100', '210200', '210300', '210900', '210400', '220100', '220200', '220900', '230100', '240100', '250100', '250200', '260100', '310000', '320000', '330000', '340000');
////            $a = [
////                '12110',
////'12120',
////'12160'
////            ];
//            $b = CircularSubject::whereIn('code', $a)->withoutGlobalScopes()->get();
//            $c = CircularItem::where('circular_id', 2)->whereIntegerInRaw('subject_id', $b->pluck('id')->toArray())->get();
//
////            dd($b->pluck('id'),$c->pluck('id'));
//
//            $bi = BudgetItem::whereIntegerInRaw('circular_item_id', $c->pluck('id')->toArray())->get();
////            dd($bi);
//////            dd($bi->where('percentage', '!=', '0.00')->first());
//            $bi->each(function ($item) {
////                $item->proposed_amount = 0;
//                $item->percentage = 10;
//
//                $item->save();
//            });
////
//////            foreach ($a as $item) {
//////                $item = BudgetItem::
//////                    joinRelationship('circularItem.subject')
//////                    ->where('bgt_circular_subjects.code', $item)
////////                        ->where('bgt_circular_subjects.name', $row['نام_حساب'])
//////                    ->addSelect('bgt_circular_items.percentage as ci_percent')
//////                    ->first();
//////                $item->proposed_amount = 0;
//////                $item->save();
//////
//////            }
//            \DB::commit();
//            dd('done');
//        } catch (\Exception $e) {
//            \DB::rollBack();
////
//            dd($e->getMessage());
//        }


//        $user = User::where('mobile', '9141852837')->first();
//        $recruitmentScripts = $user
//            ->activeRecruitmentScripts()
//            ->with(['organizationUnit'])
//            ->whereHas('scriptType', function ($query) {
//                $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
//            })->get();
////
//        $a = implode(',', ($recruitmentScripts->pluck('organizationUnit.name')->toArray()));
//        dd($a);
//
//        try {
//            $names = [
//                "دستگاه پرینتر SHARP مدل AR-X202 (تحویلی به بخشداری مرکزی پلدشت – مشترک دهیاران بخش مرکزی)",
//                "دستگاه پرینتر اسکن‌دار HP (تحویلی به بخشداری مرکزی پلدشت – مشترک دهیاران بخش مرکزی)",
//                "دستگاه حضور و غیاب ویسمن (تحویلی به بخشداری مرکزی پلدشت – مشترک دهیاران بخش مرکزی)",
//                "میز اداری قهوه‌ای ۷۰×۱۷۰ (تحویلی به بخشداری مرکزی پلدشت – مسئول امور مالی دهیاری)",
//                "مهر دهیاری",
//                "نقشه طرح هادی روستا",
//                "سطل زباله نصب‌شده در روستا (۸ عدد)",
//                "تابلو ورودی روستا"
//            ];
//
//            DB::beginTransaction();
//            $chainCode = 801001;
//            $ounitId = 1232;
//            $length = 3;
//
//            foreach ($names as $name) {
//                $parentAccount = Account::where('chain_code', $chainCode)->first();
//
//                $largest = Account::where('chain_code', 'LIKE', $chainCode . '%')
//                    ->where('ounit_id', $ounitId)
//                    ->where('parent_id', $parentAccount->id)
//                    ->orderByRaw('CAST(chain_code AS UNSIGNED) DESC')
//                    ->withoutGlobalScopes()
//                    ->activeInactive()
//                    ->first();
//                $data['segmentCode'] = addWithLeadingZeros($largest?->segment_code ?? '000', 1, $length);
////                dd($largest,$parentAccount,$data);
//                $data['name'] = $name;
//                $data['ounitID'] = $ounitId;
////                $data['isFertile'] = false;
////                $data['categoryID'] = 8;
//
//
//                $account = $this->storeAccount($data, $parentAccount);
//            }
//
//            DB::commit();
//            dd('done');
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return response()->json(['error' => 'error', $e->getMessage()], 500);
//        }
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


//        $user = User::where('mobile', '9141488599')->first();
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
//        $results = DB::table(DB::raw('(
//                WITH RECURSIVE descendants AS (
//                    SELECT id, id as root_id
//                    FROM acc_accounts
//                    WHERE id ="' . 1 . '"
//                    UNION ALL
//                    SELECT a.id, d.root_id
//                    FROM acc_accounts a
//                    INNER JOIN descendants d ON a.parent_id = d.id
//                    WHERE (a.ounit_id = ' . 5 . ' OR a.ounit_id IS NULL)
//                        )
//                SELECT * FROM descendants
//            ) as descendants'))
//            ->join('acc_articles', 'acc_articles.account_id', '=', 'descendants.id')
//            ->join('acc_documents', 'acc_documents.id', '=', 'acc_articles.document_id')
//            // Join the pivot table for statuses. This table contains the create_date and status_name.
//            ->join('accDocument_status', 'accDocument_status.document_id', '=', 'acc_documents.id')
//            ->join('statuses', 'accDocument_status.status_id', '=', 'statuses.id')
//            ->join('acc_accounts as root_account', 'root_account.id', '=', 'descendants.root_id')
//            // Ensure we only get the latest status per document
//            ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
//            // And only if that latest status has the name "active"
//            ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value)
//            ->where('acc_documents.ounit_id', 5)
//            ->where('acc_documents.fiscal_year_id', 1)
//            ->orderBy('acc_documents.document_date', 'asc')
//            ->select(
//                [
//                    'descendants.root_id',
//                    'root_account.name',
//                    'root_account.chain_code',
//                    'acc_articles.credit_amount',
//                    'acc_articles.debt_amount',
//                    'acc_articles.description',
//                    'acc_documents.document_number',
//
//                ]
//            )
//            ->get();
////
//////        $results = DB::select('with recursive `laravel_cte` as ((select `acc_accounts`.*, 0 as `depth`, cast(`id` as char(65535)) as `path` from `acc_accounts` where `acc_accounts`.`id` in (1)) union all (select `acc_accounts`.*, `depth` + 1 as `depth`, concat(`path`, \'.\', `acc_accounts`.`id`) from `acc_accounts` inner join `laravel_cte` on `laravel_cte`.`id` = `acc_accounts`.`parent_id`)) select * from `laravel_cte` ');
/////
/////
//        $credit = 0;
//        $debt = 0;
//
//        $results->each(function ($row) use (&$credit, &$debt) {
//            $credit += $row->credit_amount;
//            $debt += $row->debt_amount;
//            $row->remaining = (int)abs($credit - $debt);
//
//        });
//        dd($results);


//        DB::enableQueryLog();
//        $acc = Account::with('descendantsAndSelf')->where('acc_accounts.id', 1)->first();
//        dd($acc, DB::getQueryLog());
//        try {
//            $rows = [
////                [
////                    'cat_name' => 'بدهیهای جاری',
////                    'cat_id' => '3',
////                    'kol_name' => 'حسابها و اسناد پرداختنی تجاری',
////                    'kol_id' => '310',
////                    'segment_id' => '10',
////                    'moein_name' => 'اسناد پرداختنی',
////                    'new_chain_moein' => '31002',
////                    'm_segment_id' => '02',
////                    'tafzil_name' => 'چک های بین راهی محمود محمدی',
////                    'new_chain_tafzil' => '31002001',
////                    'taf_segment_id' => '001',
////                    'ounit_id' => 2415,
////                ],
////                [
////                    'cat_name' => 'بدهیهای جاری',
////                    'cat_id' => '3',
////                    'kol_name' => 'حسابها و اسناد پرداختنی تجاری',
////                    'kol_id' => '310',
////                    'segment_id' => '10',
////                    'moein_name' => 'اسناد پرداختنی',
////                    'new_chain_moein' => '31002',
////                    'm_segment_id' => '02',
////                    'tafzil_name' => 'چک های بین راهی بیستون علیزاده',
////                    'new_chain_tafzil' => '31002002',
////                    'taf_segment_id' => '002',
////                    'ounit_id' => 2415,
////                ], [
////                    'cat_name' => 'بدهیهای جاری',
////                    'cat_id' => '3',
////                    'kol_name' => 'حسابها و اسناد پرداختنی تجاری',
////                    'kol_id' => '310',
////                    'segment_id' => '10',
////                    'moein_name' => 'اسناد پرداختنی',
////                    'new_chain_moein' => '31002',
////                    'm_segment_id' => '02',
////                    'tafzil_name' => 'چک‌های بین راهی دهیاری سیروه',
////                    'new_chain_tafzil' => '31002001',
////                    'taf_segment_id' => '001',
////                    'ounit_id' => 2389,
////                ], [
////                    'cat_name' => 'بدهیهای جاری',
////                    'cat_id' => '3',
////                    'kol_name' => 'حسابها و اسناد پرداختنی تجاری',
////                    'kol_id' => '310',
////                    'segment_id' => '10',
////                    'moein_name' => 'اسناد پرداختنی',
////                    'new_chain_moein' => '31002',
////                    'm_segment_id' => '02',
////                    'tafzil_name' => 'چک‌های بین راهی دهیاری میکل آباد',
////                    'new_chain_tafzil' => '31002002',
////                    'taf_segment_id' => '002',
////                    'ounit_id' => 2389,
////                ], [
////                    'cat_name' => 'بدهیهای جاری',
////                    'cat_id' => '3',
////                    'kol_name' => 'حسابها و اسناد پرداختنی تجاری',
////                    'kol_id' => '310',
////                    'segment_id' => '10',
////                    'moein_name' => 'اسناد پرداختنی',
////                    'new_chain_moein' => '31002',
////                    'm_segment_id' => '02',
////                    'tafzil_name' => 'چک‌های بین راهی دهیاری کولسه سفلی',
////                    'new_chain_tafzil' => '31002003',
////                    'taf_segment_id' => '003',
////                    'ounit_id' => 2389,
////                ],
//
//                [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'لپ تاپ asus نقره ای رنگ (تحویلی بخشداری ارس)',
//                    'new_chain_tafzil' => '801001001',
//                    'taf_segment_id' => '001',
//                    'ounit_id' => 1134,
//                ],
//                [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'پرینتر سه کاره canon mf3010 سیاه رنگ (تحویلی بخشداری ارس )',
//                    'new_chain_tafzil' => '801001002',
//                    'taf_segment_id' => '002',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'لپ تاپ lenovo سیاه رنگ (تحویلی بخشداری ارس)',
//                    'new_chain_tafzil' => '801001003',
//                    'taf_segment_id' => '003',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'پرینتر تک کاره hp laser jet p 1102 سفید رنگ(تحویلی بخشداری ارس)',
//                    'new_chain_tafzil' => '801001004',
//                    'taf_segment_id' => '004',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'یخچال بوژان 12 فوتی',
//                    'new_chain_tafzil' => '801001005',
//                    'taf_segment_id' => '005',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'کامپیوتر با متعلقات',
//                    'new_chain_tafzil' => '801001006',
//                    'taf_segment_id' => '006',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'میز تحریر چوبی',
//                    'new_chain_tafzil' => '801001007',
//                    'taf_segment_id' => '007',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'صندلی فلزی 1',
//                    'new_chain_tafzil' => '801001008',
//                    'taf_segment_id' => '008',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'صندلی فلزی 2',
//                    'new_chain_tafzil' => '801001009',
//                    'taf_segment_id' => '009',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'صندلی فلزی 3',
//                    'new_chain_tafzil' => '801001010',
//                    'taf_segment_id' => '010',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'صندلی فلزی 4',
//                    'new_chain_tafzil' => '801001011',
//                    'taf_segment_id' => '011',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'صندلی فلزی 5',
//                    'new_chain_tafzil' => '801001012',
//                    'taf_segment_id' => '012',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'صندلی فلزی 6',
//                    'new_chain_tafzil' => '801001013',
//                    'taf_segment_id' => '013',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'صندلی فلزی 7',
//                    'new_chain_tafzil' => '801001014',
//                    'taf_segment_id' => '014',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'صندلی فلزی 8',
//                    'new_chain_tafzil' => '801001015',
//                    'taf_segment_id' => '015',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'میز جلسه ده نفره',
//                    'new_chain_tafzil' => '801001016',
//                    'taf_segment_id' => '016',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'میز جلو مبلی 1',
//                    'new_chain_tafzil' => '801001017',
//                    'taf_segment_id' => '017',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'میز جلو مبلی 2',
//                    'new_chain_tafzil' => '801001018',
//                    'taf_segment_id' => '018',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'فایل سه کشویی چوبی',
//                    'new_chain_tafzil' => '801001019',
//                    'taf_segment_id' => '019',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'کمد بایگانی فلزی',
//                    'new_chain_tafzil' => '801001020',
//                    'taf_segment_id' => '020',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'میز کامپیوتر چوبی',
//                    'new_chain_tafzil' => '801001021',
//                    'taf_segment_id' => '021',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'ماشین حساب سی تی زن',
//                    'new_chain_tafzil' => '801001022',
//                    'taf_segment_id' => '022',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'خط کش فلزی',
//                    'new_chain_tafzil' => '801001023',
//                    'taf_segment_id' => '023',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'پانچ',
//                    'new_chain_tafzil' => '801001024',
//                    'taf_segment_id' => '024',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'سطل زباله دهیاری',
//                    'new_chain_tafzil' => '801001025',
//                    'taf_segment_id' => '025',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'آبگرمکن',
//                    'new_chain_tafzil' => '801001026',
//                    'taf_segment_id' => '026',
//                    'ounit_id' => 1134,
//                ], [
//                    'cat_name' => 'حسابهای انتظامی',
//                    'cat_id' => '8',
//                    'kol_name' => 'حساب انتظامی دارائیها',
//                    'kol_id' => '801',
//                    'segment_id' => '01',
//                    'moein_name' => 'حساب انتظامی اثاثه',
//                    'new_chain_moein' => '801001',
//                    'm_segment_id' => '01',
//                    'tafzil_name' => 'آبگرمکن',
//                    'new_chain_tafzil' => '801001026',
//                    'taf_segment_id' => '026',
//                    'ounit_id' => 1134,
//                ],
//
//            ];
//            \DB::beginTransaction();
//            foreach ($rows as $row) {
//                $cat = AccountCategory::where('name', $row['cat_name'])
//                    ->where('id', $row['cat_id'])
//                    ->first();
//
//                $dataKol = [
//                    'name' => $row['kol_name'],
//                    'categoryID' => $cat->id,
//                    'ounitID' => null,
//                    'segmentCode' => $row['segment_id'] ?? null,
//                    'chainCode' => $row['kol_id'],
//                ];
//
//                $kolAccount = $this->firstOrStoreAccount($dataKol, null, 1);
//
//                if (isset($row['moein_name'])) {
//                    $dataMoein = [
//                        'name' => $row['moein_name'],
//                        'categoryID' => $cat->id,
//                        'ounitID' => null,
//                        'segmentCode' => $row['m_segment_id'] ?? null,
//                        'chainCode' => $row['new_chain_moein'],
//                    ];
//
//                    $moeinAccount = $this->firstOrStoreAccount($dataMoein, $kolAccount, 1);
//                }
//
//                if (isset($row['tafzil_name'])) {
//                    $dataTafzil = [
//                        'name' => $row['tafzil_name'],
//                        'categoryID' => $cat->id,
//                        'ounitID' => $row['ounit_id'],
//                        'segmentCode' => $row['taf_segment_id'] ?? null,
//                        'chainCode' => $row['new_chain_tafzil'],
//                    ];
//
//                    $tafAccount = $this->firstOrStoreAccount($dataTafzil, $moeinAccount, 1);
//                }
//
//            }
//            \DB::commit();
//            dd($moeinAccount);
//        } catch (\Exception $e) {
//            \DB::rollBack();
//            dd($e->getMessage());
//        }

    }
}
