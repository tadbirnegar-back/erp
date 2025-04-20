<?php

namespace Modules\ACC\database\seeders;

use Illuminate\Database\Seeder;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\Article;
use Modules\ACC\app\Models\GlAccount;
use Modules\ACMS\app\Models\CircularSubject;

class Budget1403AccSeeder extends Seeder
{
    use AccountTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accs = Account::where('accountable_type', GlAccount::class)
            ->where('status_id', 155)
            ->whereIn('acc_accounts.category_id', [6, 7])
            ->whereHas('articles')
            ->withoutGlobalScopes()
            ->get(['id', 'chain_code']);
        try {
            \DB::beginTransaction();
            $a = [];
            $accs->each(function ($acc) use (&$a) {
                $sb = CircularSubject::withoutGlobalScopes()->with('account')->where('code', $acc->chain_code)->first();
                if ($sb && $sb?->account->id != $acc->id) {
                    $a[] = $acc->id;
                    $articles = Article::
//                    joinRelationship('document', function ($join) {
//                        $join->where('fiscal_year_id', 1);
//                    })
//                        ->
                    where('account_id', $acc->id)->update(['account_id' => $sb->account->id]);

//                        dd($articles);
//                    $articles->each(function ($article) use ($sb) {
//                            dd($article,$sb->account);
//
//                    });
                }

            });
//dd(implode(',',$a));
            \DB::commit();
            dump('dd');
        } catch (\Exception $e) {
            \DB::rollBack();
        }
//        $pathToXlsx = realpath(__DIR__ . '/budgets acc 1403.xlsx');
//
//        $rows = SimpleExcelReader::create($pathToXlsx)
//            ->getRows();
//        \DB::beginTransaction();
//        $rows->each(function ($row) {
//            $cat = AccountCategory::where('name', $row['name_0'])
//                ->where('id', $row['code_0'])
//                ->first();
//
//            $dataKol = [
//                'name' => $row['name_1'],
//                'categoryID' => $cat->id,
//                'ounitID' => null,
//                'segmentCode' => $row['code_1'] ?? null,
//                'chainCode' => $row['code_1'],
//            ];
//
//            $kolAccount = $this->firstOrStoreAccount($dataKol, null, 1);
//            if (!empty($row['code_2'])) {
//
//                $dataMoein = [
//                    'name' => $row['name_2'],
//                    'categoryID' => $cat->id,
//                    'ounitID' => null,
//                    'segmentCode' => $row['code_2'] ?? null,
//                    'chainCode' => $row['code_2'],
//                ];
//
//                $moeinAccount = $this->firstOrStoreAccount($dataMoein, $kolAccount, 1);
//            }
//
//            if (!empty($row['code_3'])) {
//
//                $dataTafzil1 = [
//                    'name' => $row['name_3'],
//                    'categoryID' => $cat->id,
//                    'ounitID' => null,
//                    'segmentCode' => $row['code_3'] ?? null,
//                    'chainCode' => $row['code_3'],
//                ];
//
//                $tafzil1Account = $this->firstOrStoreAccount($dataTafzil1, $moeinAccount, 1);
//            }
//
//            if (!empty($row['code_4'])) {
//                $dataTafzil2 = [
//                    'name' => $row['name_4'],
//                    'categoryID' => $cat->id,
//                    'ounitID' => null,
//                    'segmentCode' => $row['code_4'] ?? null,
//                    'chainCode' => $row['code_4'],
//                ];
//
//                $tafzil2Account = $this->firstOrStoreAccount($dataTafzil2, $tafzil1Account, 1);
//            }
//
//
//        });
//        \DB::commit();
    }
}
