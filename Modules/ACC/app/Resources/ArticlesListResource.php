<?php

namespace Modules\ACC\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ACC\app\Http\Enums\AccountLayerTypesEnum;
use Modules\ACC\app\Models\DetailAccount;
use Modules\BNK\app\Models\BankAccount;

class ArticlesListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        $result = [
            'id' => $this->id,
            'description' => $this->description,
            'debt_amount' => $this->debt_amount ?? 0,
            'credit_amount' => $this->credit_amount ?? 0,
            'priority' => $this->priority,
            'transaction_id' => $this->transaction_id,
            'transaction_code' => $this->relationLoaded('transaction') && !is_null($this?->transaction) ? $this?->transaction?->cheque?->segment_number : null,
            'transaction' => $this->relationLoaded('transaction') && !is_null($this?->transaction) ? [
                'id' => $this?->transaction->id,
                'cheque' => [
                    'id' => $this?->transaction->cheque->id,
                    'payee_name' => $this->transaction->cheque->payee_name,
                    'segment_number' => $this->transaction->cheque->segment_number,
                    'due_date' => $this->transaction->cheque->due_date,
                    'status' => [
                        'name' => $this->transaction->cheque->latestStatus->name,
                        'class_name' => $this->transaction->cheque->latestStatus->class_name,
                    ],
                ],
            ] : null,
        ];

        if ($this->relationLoaded('account')) {
            $result['account'] = [
                'id' => $this->account->id,
                'name' => $this->account->name,
                'type' => AccountLayerTypesEnum::from($this->account->accountable_type)->getLabel(),
                'chain_code' => $this->account->chain_code,
                'isBankAccount' => $this->account->accountable_type == DetailAccount::class && $this->account->entity_type == BankAccount::class,
                'category' => [
                    'name' => $this->account->accountCategory->name,
                    'code' => $this->account->accountCategory->id,
                ],
                'ancestors' => $this->account->ancestorsAndSelf->isNotEmpty() ? $this->account->ancestorsAndSelf->map(function ($ancestor) {
                    return [
                        'id' => $ancestor->id,
                        'name' => $ancestor->name,
                        'chain_code' => $ancestor->chain_code,
                        'type' => AccountLayerTypesEnum::from($ancestor->accountable_type)->getLabel(),
                    ];
                }) : [],
            ];
        }

        return $result;
    }
}
