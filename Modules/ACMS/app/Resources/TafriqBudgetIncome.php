<?php

namespace Modules\ACMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TafriqBudgetIncome extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $economic = $this->total_amount * $this->next_year_percentage / 100;
        $operational = (100 - $this->next_year_percentage) * $this->total_amount / 100;


        $used = $this->total_amount - $this->next_year_proposed_amount;

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
            'total_amount' => $this->total_amount,
            'total_operational_amount' => $operational,
            'total_economic_amount' => $economic,
            'extra' => $extra,
            'deficit' => $deficit,
        ];
    }
}
