<?php

namespace Modules\BNK\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\BNK\app\Http\Enums\BankAccountStatusEnum;
use Modules\BNK\app\Models\Bank;
use Modules\BNK\app\Models\BankAccount;
use Modules\BNK\app\Models\BankBranch;
use Modules\BNK\app\Models\BnkAccountStatus;

trait BankTrait
{

    public string $bankAccountLatestStatus;

    public function __construct()
    {
        $this->bankAccountLatestStatus = BnkAccountStatus::getTableName() . '.create_date =' . '(SELECT MAX(create_date) FROM ' . BnkAccountStatus::getTableName() . ' WHERE account_id = ' . BankAccount::getTableName() . '.id)';
    }

    public function bankAccountIndex($data)
    {
        $latestStatus = $this->bankAccountLatestStatus;
        $bankAccountTable = BankAccount::getTableName();
        $bankTable = Bank::getTableName();
        $bankBranchTable = BankBranch::getTableName();

        $result = BankAccount::leftJoinRelationship('bankBranch.bank.logo')
            ->joinRelationship('statuses', [
                'statuses' => function ($join) use ($latestStatus) {
                    $join
                        ->whereRaw($latestStatus);
                }
            ])
            ->where('ounit_id', $data['ounitID'])
            ->select([
                $bankAccountTable . '.id as id',
                $bankAccountTable . '.account_number as account_number',
                $bankTable . '.name as bank_name',
                $bankBranchTable . '.name as branch_name',
                $bankBranchTable . '.branch_code as branch_code',
                $bankBranchTable . '.address as branch_address',
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'files.slug as logo_slug',
            ])
            ->get();

        return $result;

    }

    public function storeBankAccount(array $data)
    {
        $prepareData = $this->prepareBankAccountData($data);
        $bankAccount = BankAccount::create($prepareData->toArray()[0]);
        $bankAccount->statuses()->attach($this->bankAccountActivateStatus()->id);
        return $bankAccount;
    }

    public function updateBankAccount(array $data, BankAccount $bankAccount)
    {
        $data['ounitID'] = $bankAccount->ounit_id;
        $prepareData = $this->prepareBankAccountData($data);
        $bankAccount->update($prepareData->toArray()[0]);
        $account = $bankAccount->account;

        $account->name = ' حساب ' . $bankAccount->account_type_id->getLabel() . ' ' . $bankAccount->bank->name . ' ' . $bankAccount->account_number;
        $account->save();

        return $bankAccount;
    }

    public function prepareBankAccountData(array $data)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) {
            return [
                'branch_id' => $item['branchID'],
                'account_number' => $item['accountNumber'],
                'iban_number' => $item['ibanNumber'],
                'ounit_id' => $item['ounitID'],
                'register_date' => $item['registerDate'] ?? null,
                'account_type_id' => $item['accountTypeID'],
            ];
        });

        return $data;
    }

    public function bankAccountDeactivateStatus()
    {
        return Cache::rememberForever('account_inactive_bank_status', function () {
            return BankAccount::GetAllStatuses()
                ->where('name', '=', BankAccountStatusEnum::INACTIVE->value)
                ->first();
        });
    }

    public function bankAccountActivateStatus()
    {
        return Cache::rememberForever('account_active_bank_status', function () {
            return BankAccount::GetAllStatuses()
                ->where('name', '=', BankAccountStatusEnum::ACTIVE->value)
                ->first();
        });
    }
}
