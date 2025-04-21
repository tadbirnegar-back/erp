<?php

namespace Modules\ACC\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Models\Account;

class InsertAccountSeeder extends Seeder
{
    use AccountTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $names = [
                'ودایع دریافتی'
            ];

//            DB::beginTransaction();
            $chainCode = 312;
            $ounitId = null;
            $length = 2;

            foreach ($names as $name) {
                $parentAccount = Account::where('chain_code', $chainCode)->first();

                $largest = $this->latestAccountByChainCode($chainCode, $ounitId);
//                dd($largest);
                $data['segmentCode'] = addWithLeadingZeros($largest?->segment_code ?? '000', 1, $length);
//                dd($largest,$parentAccount,$data);
                $data['name'] = $name;
                $data['ounitID'] = $ounitId;
                $data['isFertile'] = true;
//                $data['categoryID'] = 8;


                $account = $this->storeAccount($data, $parentAccount);
                dump($account->chain_code);
            }

//            DB::commit();
            dd('done');
        } catch (\Exception $e) {
            DB::rollBack();
            dd(['error' => 'error', $e->getMessage()], 500);
        }
    }
}
