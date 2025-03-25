<?php

namespace Modules\ACC\database\seeders;

use Illuminate\Database\Seeder;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Models\AccountCategory;
use Spatie\SimpleExcel\SimpleExcelReader;

class AccountImportSeeder extends Seeder
{
    use AccountTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pathToXlsx = realpath(__DIR__ . '/Samad Accounts List STD fixed.xlsx');

        $rows = SimpleExcelReader::create($pathToXlsx)
            ->getRows();
        try {
            \DB::beginTransaction();
            $rows->each(function ($row) {
//                $moein = Account::where('accountable_type', SubAccount::class)
//                    ->where('name', $row['moein_name'])
//                    ->where('chain_code', $row['moein_id'])
//                    ->with(['children' => function ($q) {
//                        $q->withoutGlobalScopes();
//
//                    }])
//                    ->first();
//
//                $newUsedChains = Account::where('new_chain_code', $moein->chain_code)->withoutGlobalScopes()->get();
//
//                $moein->chain_code = $row['new_chain_moein'];
//                $moein->segment_code = $row['m_segment_id'];
//                $moein->save();
//
////                $children = Account::where('parent_id', $moein->id)->withoutGlobalScopes()->get();
//                $children = $moein->children;
//
//                if ($children->isNotEmpty()) {
//                    $children->each(function ($child) use ($moein) {
//                        $newUsedChildChains = Account::where('new_chain_code', $child->chain_code)->withoutGlobalScopes()->get();
//                        $child->chain_code = $moein->chain_code . $child->segment_code;
////                        $child->new_chain_code = null;
//                        $child->save();
//
//                        if ($newUsedChildChains->isNotEmpty()) {
//                            $newUsedChildChains->each(function ($usedChild) use ($child) {
//                                $usedChild->new_chain_code = $child->chain_code;
//
//                                $usedChild->save();
//                            });
//                        }
//                    });
//                }
////
//                if ($newUsedChains->isNotEmpty()) {
//                    $newUsedChains->each(function ($item) use ($moein) {
//                        $item->new_chain_code = $moein->chain_code;
//
//                        $item->save();
//                    });
//                }
//
                $cat = AccountCategory::where('name', $row['cat_name'])
                    ->where('id', $row['cat_id'])
                    ->first();

                $dataKol = [
                    'name' => $row['kol_name'],
                    'categoryID' => $cat->id,
                    'ounitID' => null,
                    'segmentCode' => $row['segment_id'] ?? null,
                    'chainCode' => $row['kol_id'],
                ];

                $kolAccount = $this->firstOrStoreAccount($dataKol, null, 1);

                $dataMoein = [
                    'name' => $row['moein_name'],
                    'categoryID' => $cat->id,
                    'ounitID' => null,
                    'segmentCode' => $row['m_segment_id'] ?? null,
                    'chainCode' => $row['new_chain_moein'],
                ];

                $moeinAccount = $this->firstOrStoreAccount($dataMoein, $kolAccount, 1);


            });
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            dd($e->getMessage(), $e->getTrace());
        }

    }
}
