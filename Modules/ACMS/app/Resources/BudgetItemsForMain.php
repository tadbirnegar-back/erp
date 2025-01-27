<?php

namespace Modules\ACMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BudgetItemsForMain extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'amount' => $this->next_year_proposed_amount,
            'ancestors' => $this->ancestors,
            'last_year_confirmed' => $this->total_amount,
            'percentage' => $this->next_year_percentage,
            'current_year_enacted' => $this->current_year_proposed_amount,
            '3_month_last_year' => $this->three_months_last_year_proposed_amount,
            '9_month_current_year' => $this->nine_months_current_year_proposed_amount,
            'budget_item_id' => $this->next_year_budget_item_id,

        ];
    }
}
