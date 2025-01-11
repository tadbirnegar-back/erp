<?php

namespace Modules\ACC\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ACC\app\Http\Enums\AccountLayerTypesEnum;

class ArticlesListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        return [
            'id' => $this->id,
            'description' => $this->description,
            'debt_amount' => $this->debt_amount ?? 0,
            'credit_amount' => $this->credit_amount ?? 0,
            'priority' => $this->priority,
            'account' => [
                'id' => $this->account->id,
                'name' => $this->account->name,
                'type' => AccountLayerTypesEnum::from($this->account->accountable_type)->getLabel(),
                'chain_code' => $this->account->chain_code,
                'ancestors' => $this->account->ancestorsAndSelf->isNotEmpty() ? $this->account->ancestorsAndSelf->map(function ($ancestor) {
                    return [
                        'id' => $ancestor->id,
                        'name' => $ancestor->name,
                        'type' => AccountLayerTypesEnum::from($ancestor->accountable_type)->getLabel(),
                        'category' => [
                            'name' => $ancestor->accountCategory->name,
                            'code' => $ancestor->accountCategory->id,
                        ],
                    ];
                }) : [],
            ]
        ];
    }
}
