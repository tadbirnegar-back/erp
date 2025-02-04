<?php

namespace Modules\BNK\app\Http\Traits;

use Modules\ACC\app\Http\Enums\TransactionStatusEnum;
use Modules\BNK\app\Models\Transaction;

trait TransactionTrait
{
    public function storeTransaction(array $data)
    {
        $preparationData = $this->prepareTransactionData($data);
        $transaction = Transaction::create($preparationData->toArray()[0]);

        return $transaction;
    }

    public function prepareTransactionData(array $data)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        $data = collect($data)->map(function ($item) {
            return [
                'deposit' => $item['deposit'] ?? null,
                'withdrawal' => $item['withdrawal'] ?? null,
                'transfer' => $item['transfer'] ?? null,
                'transactionable_id' => $item['transactionableID'] ?? null,
                'transactionable_type' => $item['transactionableType'] ?? null,
                'bank_account_id' => $item['bankAccountID'],
                'creator_id' => $item['userID'],
                'cheque_id' => $item['chequeID'] ?? null,
                'card_id' => $item['cardID'] ?? null,
                'status_id' => $item['statusID'],
                'isSynced' => $item['isSynced'] ?? false,
                'create_date' => $item['createDate'] ?? now(),
            ];
        });

        return $data;
    }

    public function transactionActiveStatus()
    {
        return Transaction::GetAllStatuses()->where('name', TransactionStatusEnum::ACTIVE->value)->first();
    }

    public function transactionDeleteStatus()
    {
        return Transaction::GetAllStatuses()->where('name', TransactionStatusEnum::DELETED->value)->first();
    }
}
