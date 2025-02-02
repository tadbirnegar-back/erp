<?php

namespace Modules\BNK\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $result = [
            'id' => $this->id,
            'account_number' => $this->account_number,
            'iban_number' => $this->iban_number,
            'account_type' => $this->account_type_id->getLabelAndValue(),
            'register_date' => $this->register_date,

        ];
        if ($this->relationLoaded('bankBranch')) {
            $result['branch'] = [
                'id' => $this?->bankBranch->id,
                'name' => $this?->bankBranch->name,
                'branch_code' => $this?->bankBranch->branch_code,
                'address' => $this?->bankBranch->address,
            ];

            if ($this->bankBranch->relationLoaded('bank')) {
                $result['bank'] = [
                    'id' => $this?->bankBranch->bank->id,
                    'name' => $this?->bankBranch->bank->name,
                    'logo' => [
                        'slug' => $this?->bankBranch->bank->logo->slug,
                    ]
                ];
            }
        }

        if ($this->relationLoaded('ounit')) {
            $result['ounit'] = [
                'id' => $this?->ounit->id,
                'name' => $this?->ounit->name,
                'abadi_code' => $this?->ounit->village->abadi_code,
                'ancestors' => $this?->ounit->ancestors->map(function ($ounit) {
                    return [
                        'name' => $ounit?->name,
                    ];
                }),
            ];
        }

        if ($this->relationLoaded('latestStatus')) {
            $result['status'] = [
                'name' => $this?->latestStatus->name,
                'class_name' => $this?->latestStatus->class_name,
            ];
        }
        if ($this->relationLoaded('chequeBooks')) {
            $result['cheque_books'] = $this?->chequeBooks;
        }
        if ($this->relationLoaded('accountCards')) {
            $result['account_cards'] = $this?->accountCards;
        }

        return $result;
    }


}
