<?php

namespace App\Http\Controllers;


use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Http\Traits\ArticleTrait;
use Modules\ACC\app\Http\Traits\DocumentTrait;
use Modules\ACMS\app\Http\Trait\CircularSubjectsTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\BNK\app\Http\Traits\BankTrait;
use Modules\BNK\app\Http\Traits\ChequeTrait;
use Modules\BNK\app\Http\Traits\TransactionTrait;
use Modules\Gateway\app\Models\Payment;
use Modules\HRMS\app\Http\Traits\JobTrait;
use Modules\HRMS\app\Http\Traits\LevelTrait;
use Modules\HRMS\app\Http\Traits\PositionTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\StatusMS\app\Models\Status;

class testController extends Controller
{
//    use BankTrait, ChequeTrait, TransactionTrait, FiscalYearTrait, DocumentTrait, AccountTrait, ArticleTrait, CircularSubjectsTrait;
//    use JobTrait, PositionTrait, LevelTrait, JobTrait, RecruitmentScriptTrait;

    /**
     * Execute the job.
     */
//    function updateDescendants($parent, $children)
//    {
//        // Optional: update the parent if needed
//        // $parent->field = 'newValue';
//        // $parent->save();
//
//        foreach ($children as $child) {
//            // Update the child
//
//
//            // Check if the child has its own children
//            if ($child->children && $child->children->isNotEmpty()) {
//                $this->updateDescendants($child, $child->children);
//            }
//        }
//    }

    public function run()
    {
        $status = Status::where('model', Payment::class)->where('name', 'پرداخت شده')->first();


        $query = OrganizationUnit::join('village_ofcs', function ($join) {
            $join->on('village_ofcs.id', '=', 'organization_units.unitable_id')
                ->where('organization_units.unitable_type', VillageOfc::class);
        })
            ->join('payments', 'payments.organization_unit_id', '=', 'organization_units.id')
            ->join('organization_units as town', 'town.id', 'organization_units.parent_id')
            ->join('organization_units as district', 'town.parent_id', '=', 'district.id')
            ->join('organization_units as city', 'city.id', 'district.parent_id')
            ->select([
                'village_ofcs.id as village_id',
                'payments.id as payment_id',
                'payments.amount as payment_amount',
                'payments.create_date as payment_date',
                'payments.transactionid as txID',
                'village_ofcs.ofc_code as national_code',
                'organization_units.name as ounit_name',
                'district.name as district_name',
                'city.name as city_name'
            ])
            ->withoutGlobalScopes()
            ->where('organization_units.unitable_type', VillageOfc::class)
            ->where('payments.status_id', $status->id)
            ->orderBy('payment_id')
            ->get();


        echo "<table border='1'>
    <thead>
        <tr>
            <th>نام روستا</th>
            <th>مقدار پرداختی</th>
            <th>تاریخ</th>
            <th>کد تراکنش</th>
            <th>شناسه ملی</th>
            <th>آدرس</th>
        </tr>
    </thead>
    <tbody>";

        foreach ($query as $item) {
            $date = convertDateTimeGregorianToJalaliDateTime($item->payment_date);
            echo "<tr>
        <td>{$item->ounit_name}</td>
        <td>{$item->payment_amount}</td>
        <td>{$date}</td>
        <td>{$item->txID}</td>
        <td>{$item->national_code}</td>
        <td>" . $item->city_name . "، " . $item->district_name . "، " . $item->ounit_name . "</td>
    </tr>";
        }


        echo "</tbody></table>";


//        $accs = Account::where('accountable_type', GlAccount::class)
//            ->where('status_id', 155)
//            ->whereIn('acc_accounts.category_id', [6, 7])
//            ->whereHas('articles')
//            ->withoutGlobalScopes()
//            ->get(['id', 'chain_code']);
////        dd($accs);
//        try {
//            \DB::beginTransaction();
//            $a = [];
//            $accs->each(function ($acc) use (&$a) {
//                $sb = CircularSubject::withoutGlobalScopes()->with('account')->where('code', $acc->chain_code)->first();
//                if ($sb && $sb?->account->id != $acc->id) {
//                    $a[] = $acc->id;
//////                    $articles = Article::joinRelationship('document', function ($join) {
//////                        $join->where('fiscal_year_id', 1);
//////                    })->where('account_id', $acc->id)->update(['account_id' => $sb->account->id]);
////
//////                        dd($articles);
//////                    $articles->each(function ($article) use ($sb) {
//////                            dd($article,$sb->account);
//////
//////                    });
//                }
//
//            });
//            dd($a);
//////dd(implode(',',$a));
//            \DB::commit();
//            dd('dd');
//        } catch (\Exception $e) {
//            \DB::rollBack();
//        }


//// Collect IDs from accounts
//        $accountIds = $accs->pluck('id');
//
//// Prepare a collection to hold articles
//        $articles = collect();
//
//// Chunk the IDs into groups (e.g., 500 per chunk)
//        $accountIds->chunk(50)->each(function ($chunkIds) use (&$articles) {
//            $chunkArticles = Article::joinRelationship('document', function ($join) {
//                $join->where('fiscal_year_id', 1);
//            })
//                ->whereIntegerInRaw('account_id', $chunkIds->toArray())
//                ->get();
//
//            // Merge current chunk results into the main collection
//            $articles = $articles->merge($chunkArticles);
//            dd($articles);
//        });
//
//        dd($articles);
//        dd($articles->pluck('id'));
//        $articles = Account::withoutGlobalScopes()
//            ->where('acc_accounts.accountable_type', GlAccount::class)
//            ->withoutGlobalScopes()
////                ->whereNull('acc_accounts.ounit_id')
//            ->whereIn('acc_accounts.category_id', [6, 7])
//            ->where('acc_accounts.status_id', 155)
//        ->joinRelationship('articles.document', [
//            'document'=>function ($join) {
//                $join
//                    ->where('fiscal_year_id', '=', 1);
//            }
//        ])
//
//            ->toRawSql();

//        $articles = Document::joinRelationship('articles.account', [
//            'account' => function ($join) {
//                $join->where('acc_accounts.accountable_type', GlAccount::class)
//                    ->whereIn('acc_accounts.category_id', [6, 7])
//                    ->where('acc_accounts.status_id', 155);
//            }
//        ])
//            ->select('acc_articles.account_id')
//            ->where('fiscal_year_id', '=', 1)
//        ->get();
//        dd($articles);

//        try {
//            \DB::beginTransaction();
//////            $new40darsad = array('11150', '11210', '11220', '11230', '12140', '12191', '12192', '12194', '12195', '12196', '12210', '12230', '12140');
////
//////            $new40darsad = array(12250, 13110, 13170, 13190, 13195, 13196, 13220, 15110, 15120, 21010);
//////            $10darsad = array(16240);
////            $zerodarsad = array(12193);
//            $zero = array('210100', '210200', '210300', '210900', '210400', '220100', '220200', '220900', '230100', '240100', '250100', '250200', '260100', '310000', '320000', '330000', '340000');
//////            $40darsad = [
//////                '12110',
//////'12120',
//////'12160'
//////            ];
//            $b = CircularSubject::whereIn('code', $zero)->withoutGlobalScopes()->get();
//            $c = CircularItem::whereIntegerInRaw('subject_id', $b->pluck('id')->toArray())->get();
//
////            dd($b->pluck('id'),$c->pluck('id'));
//
//            $bi = BudgetItem::whereIntegerInRaw('circular_item_id', $c->pluck('id')->toArray())->get();
////            dd($bi);
//////            dd($bi->where('percentage', '!=', '0.00')->first());
//            $bi->each(function ($item) {
//                $item->proposed_amount = 0;
////                $item->percentage = 0;
//
//                $item->save();
//            });
//
//            \DB::commit();
//            dd('done');
//        } catch (\Exception $e) {
//            \DB::rollBack();
////
//            dd($e->getMessage());
//        }

//
//        $user = User::whereIntegerInRaw('mobile', ['9148005144',])->with([
//            'activeRecruitmentScripts' => function ($query) {
//                $query->with(['organizationUnit'])
//                    ->whereHas('scriptType', function ($query) {
//                        $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
//                    });
//            }
//
//        ])->get();
////        dd($user);
//        $recruitmentScripts = $user->pluck('activeRecruitmentScripts')->flatten(1);
////        $recruitmentScripts = $user
////            ->activeRecruitmentScripts()
////            ->with(['organizationUnit'])
////            ->whereHas('scriptType', function ($query) {
////                $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
////            })->get();
////
//        $a = implode(',', ($recruitmentScripts->pluck('organization_unit_id')->toArray()));
//        dd($a);
//
    }
}
