<?php

namespace Modules\ACMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BudgetItemsForSupplementary extends JsonResource
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
            'current_year_amount' => $this->current_year_proposed_amount,
            'last_year_confirmed' => $this->total_amount,
            'percentage' => $this->next_year_percentage,
            'ancestors' => $this->ancestors,
            'budget_item_id' => $this->next_year_budget_item_id,

        ];
    }
}
