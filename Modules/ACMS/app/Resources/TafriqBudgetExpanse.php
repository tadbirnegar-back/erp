<?php

namespace Modules\ACMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TafriqBudgetExpanse extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        $used = abs($this->total_operational_amount) + abs($this->total_economic_amount) - $this->next_year_proposed_amount;

        if ($used < 0) {
            $extra = 0;
            $deficit = abs($used);
        } else {
            $deficit = 0;
            $extra = abs($used);
        }
        return [
            'name' => $this->name,
            'code' => $this->code,
            'enacted' => $this->next_year_proposed_amount,
            'ancestors' => $this->ancestors,
            'percentage' => $this->next_year_percentage,
            'total_operational_amount' => abs($this->total_operational_amount),
            'total_economic_amount' => abs($this->total_economic_amount),
            'extra' => $extra,
            'deficit' => $deficit,
        ];
    }
}
