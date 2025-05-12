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
use Modules\HRMS\app\Http\Traits\JobTrait;
use Modules\HRMS\app\Http\Traits\LevelTrait;
use Modules\HRMS\app\Http\Traits\PositionTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Person;

class testController extends Controller
{
    use BankTrait, ChequeTrait, TransactionTrait, FiscalYearTrait, DocumentTrait, AccountTrait, ArticleTrait, CircularSubjectsTrait;
    use JobTrait, PositionTrait, LevelTrait, JobTrait, RecruitmentScriptTrait, PersonTrait;

    /**
     * Execute the job.
     */
    function updateDescendants($parent, $children)
    {
        // Optional: update the parent if needed
        // $parent->field = 'newValue';
        // $parent->save();

//        foreach ($children as $child) {
//            // Update the child
//
//
//            // Check if the child has its own children
//            if ($child->children && $child->children->isNotEmpty()) {
//                $this->updateDescendants($child, $child->children);
//            }
//        }
    }


    function run()
    {

        $p = Person::where('id', 2604)->first();
        dd($p->militaryService->militaryServiceStatus);

        $a = OrganizationUnit::joinRelationship('village')
            ->with('ancestors')
            ->where('unitable_type', VillageOfc::class)
            ->where('village_ofcs.hasLicense', 1)
            ->select([
                'organization_units.id',
                'organization_units.parent_id',
                'organization_units.name as village_name',
                'village_ofcs.abadi_code as village_abadi_code'
            ])
            ->get();
        dump($a);
//        $p = Person::find(1908);
//        dd($p->latestEducationRecord->levelOfEducation);
//        $script = RecruitmentScript::with(
//            [
//                'person' => function ($query) {
//                    $query->with(['natural', 'isar', 'militaryService' => function ($query) {
//                        $query->with(['exemptionType', 'militaryServiceStatus']);
//                    }]);
//
//                },
//                'ounit.ancestors' => function ($query) {
//                    $query->where('unitable_type', '!=', TownOfc::class);
//                },
//                'scriptAgents.scriptAgentType',
//                'latestEducationRecord',
//                'position'
//            ]
//        )
//            ->withCount('heirs')
//            ->find(5273);
////        dd($script);
//        return RecruitmentScriptContractResource::make($script);
//
//
//        $a = ScriptTypesEnum::VILLAGER;
//        $b = HireTypeEnum::PART_TIME;
//        $st = ScriptType::where('title', $a->value)->first();
//        $ht = HireType::where('title', $b->value)->first();
//        $class = 'Modules\HRMS\app\Calculations\\' . $a->getCalculateClassPrefix() . 'ScriptType' . $b->getCalculateClassPrefix() . 'HireTypeCalculator';
//        $ounit = OrganizationUnit::with(['ancestors' => function ($query) {
//            $query->where('unitable_type', '!=', TownOfc::class);
//        }])->find(5);
//
//        dump(
//            $ounit->ancestors[0],
//            $ounit->ancestors[1],
//            $ounit->ancestors[2]
//        );
//
//        $person = Person::where('personable_type', Natural::class)->first();
//        dump($person->personable);
//
//        $x = new $class($st, $ht, $ounit);
//        $formulas = collect(FormulaEnum::cases());
//
//        $res = $formulas->map(function ($formula) use ($x) {
//            $fn = $formula->getFnName();
//            $res = $x->$fn();
//            return [
//                'default_value' => $res,
//                'label' => $formula->getLabel(),
//            ];
//
//        });

//        $enactments = Enactment::joinRelationship('statuses', ['statuses' => function ($join) {
//            $join
//                ->whereRaw('enactment_status.create_date = (SELECT MAX(create_date) FROM enactment_status WHERE enactment_id = enactments.id)')
//                ->where('statuses.name', '=', EnactmentStatusEnum::PENDING_FOR_BOARD_DATE->value);
//        }])
//            ->with(['onlyHeyaatMeeting'])
//            ->get();
//
//        $mts = [];

//        $enactments->each(function ($enactment) use (&$mts) {
//            if (!in_array($enactment->onlyHeyaatMeeting->id, $mts)) {
//                $meetingDate3 = convertDateTimeHaveDashJalaliPersianCharactersToGregorian($enactment->onlyHeyaatMeeting->meeting_date);
//
//                $alertDate = Carbon::parse($meetingDate3)->subDays(1);
//
//                StoreMeetingJob::dispatch($enactment->onlyHeyaatMeeting->id)->delay($alertDate);
//                $mts[] = $enactment->onlyHeyaatMeeting->id;
//            }
//
//            $meetingDate1 = $enactment->onlyHeyaatMeeting->getRawOriginal('meeting_date');
//            $meetingDate2 = $enactment->onlyHeyaatMeeting->getRawOriginal('meeting_date');
//            $meetingDate3 = $enactment->onlyHeyaatMeeting->getRawOriginal('meeting_date');
//
//            $delayHeyat = Carbon::parse($meetingDate1)->setTime(23, 50, 0);
//            $delayKarshenas = Carbon::parse($meetingDate2)->setTime(23, 50, 0);
//            StoreEnactmentStatusJob::dispatch($enactment->id)->delay($delayHeyat);
//            StoreEnactmentStatusKarshenasJob::dispatch($enactment->id)->delay($delayKarshenas);
//            $delayPending = Carbon::parse($meetingDate3);

//            PendingForHeyaatStatusJob::dispatch($enactment->id)->delay($delayPending);


//        });
//        dd('done');
//
//        dd($enactments);
//        $enacts = [2615, 2553, 2552, 2551, 2547, 2546, 2545, 2544, 2543, 2542, 2492, 2485, 2483, 2482, 2481, 2480, 2479, 2478, 2477, 2476, 2475, 2474, 2473, 2456, 2431, 2430, 2429, 2427, 2426, 2424, 2423, 2422, 2421, 2420, 2419, 2418, 2415, 2414, 2413, 2412, 2254, 2253, 2252, 2251, 2242, 2241, 2240, 2239, 2238, 2237,2238,2237,2236,2235,2234,];
//
////        $enacts = [2425];
//
//        foreach ($enacts as $enact) {
//
//            StoreEnactmentStatusJob::dispatch($enact)->onQueue('High');
//            StoreEnactmentStatusKarshenasJob::dispatch($enact)->onQueue('default');
//            echo "enactment $enact\n";
//        }
//        dd(User::first());

//        $ounits = [444
//        ];
//        foreach ($ounits as $ounit) {
//            for ($i = 1; $i <= 3; $i++) {
//                $fys = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
//                foreach ($fys as $fy) {
//                    Cache::forget("last_year_confirmed_documents_ounit_{$ounit}_year_{$fy}_subject_type_{$i}");
//
//                    Cache::forget("three_months_two_years_ago_ounit_{$ounit}_year_{$fy}_subject_type_{$i}");
//
//                    Cache::forget("nine_month_last_year_ounit_{$ounit}_year_{$fy}_subject_type_{$i}");
//                }
//
//
//            }
//
//        }
//        dd($ounits);

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
//        $user = User::whereIntegerInRaw('mobile', ['9144834285',])->with([
//            'activeRecruitmentScripts' => function ($query) {
//                $query->with(['organizationUnit'])
//                    ->whereHas('scriptType', function ($query) {
//                        $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
//                    });
//            }
//
//        ])->get();
//
//        $recruitmentScripts = $user->pluck('activeRecruitmentScripts')->flatten(1);
//
//        $a = implode(',', ($recruitmentScripts->pluck('employee_id')->toArray()));
//        dd($a);
//
    }
}
