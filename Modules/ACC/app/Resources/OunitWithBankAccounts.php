<?php

namespace Modules\ACC\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OunitWithBankAccounts extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'ounitName' => $this->name,
            'abadiCode' => $this->village->abadi_code,
            'ancestors' => $this->ancestors->map(function ($ancestor) {
                return [
                    'name' => $ancestor->name,
                ];
            }),
            'bankAccounts' => $this->accounts->map(function ($account) {
                return [
                    'id' => $account->id,
                    'name' => $account->name,
                ];
            }),
        ];
    }
}
