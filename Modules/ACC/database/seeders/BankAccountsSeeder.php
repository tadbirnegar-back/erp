<?php

namespace Modules\ACC\database\seeders;

use Illuminate\Database\Seeder;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\SubAccount;
use Modules\BNK\app\Http\Enums\BankAccountTypeEnum;
use Modules\BNK\app\Http\Traits\BankTrait;
use Modules\BNK\app\Models\Bank;
use Modules\BNK\app\Models\BankBranch;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Spatie\SimpleExcel\SimpleExcelReader;

class BankAccountsSeeder extends Seeder
{
    use BankTrait, AccountTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pathToXlsx = realpath(__DIR__ . '/all villages bank accs.xlsx');
//        dd($pathToXlsx);
        $rows = SimpleExcelReader::create($pathToXlsx)
            ->getRows();
        $keshavarziBank = 'بانک کشاورزی';
        $postBank = 'پست بانک';
        $saderatBank = 'بانک صادرات';
        $sepahBank = 'بانک سپه';

        $bankKesh = Bank::firstOrCreate(['name' => $keshavarziBank]);
        $bankPost = Bank::firstOrCreate(['name' => $postBank,]);
        $bankSaderat = Bank::firstOrCreate(['name' => $saderatBank]);
        $bankSepah = Bank::firstOrCreate(['name' => $sepahBank]);
        $moeinBanks = Account::where('accountable_type', SubAccount::class)
            ->where('name', 'بانکها')->where('chain_code', '11005')->first();

//        dd($moeinBanks);
        \DB::beginTransaction();
        $rows->each(function ($row) use ($bankKesh, $bankPost, $bankSaderat, $bankSepah, $moeinBanks) {
            $ounit = OrganizationUnit::joinRelationship('village', function ($join) use ($row) {
                $join->where('abadi_code', $row['abadi_code']);
            })
                ->where('name', $row['village'])
                ->first();
            if (is_null($ounit)) {
                dd($row);
            }

            if (!empty($row['keshavarzi_acc_num'])) {
                $keshavarziBranch = BankBranch::firstOrCreate([
                    'bank_id' => $bankKesh->id,
                    'name' => $row['keshavarzi_shobe_name'],
                    'branch_code' => $row['keshavarzi_shobe_code'],
                ]);

                $keshData = [
                    'branchID' => $keshavarziBranch->id,
                    'accountNumber' => str_replace('.', '', $row['keshavarzi_acc_num']),
                    'ibanNumber' => str_replace('IR', '', $row['keshavarzi_shaba']),
                    'ounitID' => $ounit->id,
                    'accountTypeID' => BankAccountTypeEnum::CURRENT->value,
                ];

                $keshBankAccount = $this->storeBankAccount($keshData);
                $acc = Account::where('accountable_type', SubAccount::class)
                    ->where('chain_code', '11005')
                    ->first();

                $largest = Account::where('chain_code', 'LIKE', '11005%')
                    ->where('entity_type', get_class($keshBankAccount))
                    ->where('ounit_id', $keshBankAccount->ounit_id)
                    ->orderByRaw('CAST(chain_code AS UNSIGNED) DESC')
                    ->withoutGlobalScopes()
                    ->first();
                $data = [
                    'name' => ' حساب ' . $keshBankAccount->account_type_id->getLabel() . ' ' . $keshBankAccount->bank->name . ' ' . $keshBankAccount->account_number,
                    'ounitID' => $keshBankAccount->ounit_id,
                    'segmentCode' => addWithLeadingZeros($largest?->segment_code ?? '000', 1),
                    'entityType' => get_class($keshBankAccount),
                    'entityID' => $keshBankAccount->id,
                ];
                $accBankAccount = $this->storeAccount($data, $moeinBanks);
            }

            if (!empty($row['postBank_acc_num'])) {
                $keshavarziBranch = BankBranch::firstOrCreate([
                    'bank_id' => $bankPost->id,
                    'name' => $row['postBank_shobe_name'],
                    'branch_code' => $row['postBank_shobe_code'],
                ]);

                $keshData = [
                    'branchID' => $keshavarziBranch->id,
                    'accountNumber' => str_replace('.', '', $row['postBank_acc_num']),
                    'ibanNumber' => str_replace('IR', '', $row['postBank_shaba']),
                    'ounitID' => $ounit->id,
                    'accountTypeID' => BankAccountTypeEnum::CURRENT->value,
                ];

                $keshBankAccount = $this->storeBankAccount($keshData);
                $acc = Account::where('accountable_type', SubAccount::class)
                    ->where('chain_code', '110005')
                    ->first();

                $largest = Account::where('chain_code', 'LIKE', '11005%')
                    ->where('entity_type', get_class($keshBankAccount))
                    ->where('ounit_id', $keshBankAccount->ounit_id)
                    ->orderByRaw('CAST(chain_code AS UNSIGNED) DESC')
                    ->withoutGlobalScopes()
                    ->first();
                $data = [
                    'name' => ' حساب ' . $keshBankAccount->account_type_id->getLabel() . ' ' . $keshBankAccount->bank->name . ' ' . $keshBankAccount->account_number,
                    'ounitID' => $keshBankAccount->ounit_id,
                    'segmentCode' => addWithLeadingZeros($largest?->segment_code ?? '000', 1),
                    'entityType' => get_class($keshBankAccount),
                    'entityID' => $keshBankAccount->id,
                ];
                $accBankAccount = $this->storeAccount($data, $moeinBanks);
            }

            if (!empty($row['saderat_acc_num'])) {
                $keshavarziBranch = BankBranch::firstOrCreate([
                    'bank_id' => $bankSaderat->id,
                    'name' => $row['saderat_shobe_name'],
                    'branch_code' => $row['saderat_shobe_code'],
                ]);

                $keshData = [
                    'branchID' => $keshavarziBranch->id,
                    'accountNumber' => $row['saderat_acc_num'],
                    'ibanNumber' => str_replace('IR', '', $row['saderat_shaba']),
                    'ounitID' => $ounit->id,
                    'accountTypeID' => BankAccountTypeEnum::CURRENT->value,
                ];

                $keshBankAccount = $this->storeBankAccount($keshData);
                $acc = Account::where('accountable_type', SubAccount::class)
                    ->where('chain_code', '11005')
                    ->first();

                $largest = Account::where('chain_code', 'LIKE', '11005%')
                    ->where('entity_type', get_class($keshBankAccount))
                    ->where('ounit_id', $keshBankAccount->ounit_id)
                    ->orderByRaw('CAST(chain_code AS UNSIGNED) DESC')
                    ->withoutGlobalScopes()
                    ->first();
                $data = [
                    'name' => ' حساب ' . $keshBankAccount->account_type_id->getLabel() . ' ' . $keshBankAccount->bank->name . ' ' . $keshBankAccount->account_number,
                    'ounitID' => $keshBankAccount->ounit_id,
                    'segmentCode' => addWithLeadingZeros($largest?->segment_code ?? '000', 1),
                    'entityType' => get_class($keshBankAccount),
                    'entityID' => $keshBankAccount->id,
                ];
                $accBankAccount = $this->storeAccount($data, $moeinBanks);
            }

            if (!empty($row['sepah_acc_num'])) {
                $keshavarziBranch = BankBranch::firstOrCreate([
                    'bank_id' => $bankSepah->id,
                    'name' => $row['sepah_shobe_name'],
                    'branch_code' => $row['sepah_shobe_code'],
                ]);

                $keshData = [
                    'branchID' => $keshavarziBranch->id,
                    'accountNumber' => str_replace('.', '', $row['sepah_acc_num']),
                    'ibanNumber' => str_replace('IR', '', $row['sepah_shaba']),
                    'ounitID' => $ounit->id,
                    'accountTypeID' => BankAccountTypeEnum::CURRENT->value,
                ];

                $keshBankAccount = $this->storeBankAccount($keshData);
                $acc = Account::where('accountable_type', SubAccount::class)
                    ->where('chain_code', '11005')
                    ->first();

                $largest = Account::where('chain_code', 'LIKE', '11005%')
                    ->where('entity_type', get_class($keshBankAccount))
                    ->where('ounit_id', $keshBankAccount->ounit_id)
                    ->orderByRaw('CAST(chain_code AS UNSIGNED) DESC')
                    ->withoutGlobalScopes()
                    ->first();
                $data = [
                    'name' => ' حساب ' . $keshBankAccount->account_type_id->getLabel() . ' ' . $keshBankAccount->bank->name . ' ' . $keshBankAccount->account_number,
                    'ounitID' => $keshBankAccount->ounit_id,
                    'segmentCode' => addWithLeadingZeros($largest?->segment_code ?? '000', 1),
                    'entityType' => get_class($keshBankAccount),
                    'entityID' => $keshBankAccount->id,
                ];
                $accBankAccount = $this->storeAccount($data, $moeinBanks);
            }


        });
        \DB::commit();
    }
}
