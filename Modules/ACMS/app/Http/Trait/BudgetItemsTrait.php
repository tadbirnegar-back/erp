<?php

namespace Modules\ACMS\app\Http\Trait;

use Illuminate\Support\Collection;
use Modules\ACMS\app\Models\Budget;
use Modules\ACMS\app\Models\BudgetItem;

trait BudgetItemsTrait
{
    public function bulkStoreBudgetItems(Collection|array $data, array $circulars)
    {

        foreach ($data as $datum) {
            $preparadoData = $this->budgetItemsDataPreparation($datum, $circulars);

            $circularItems = BudgetItem::insert($preparadoData->toArray());
        }

        return $circularItems;
    }

    public function budgetItemsDataPreparation(Budget $budget, array $data)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) use ($budget) {

            return [
                'budget_id' => $budget->id,
                'circular_item_id' => $item['id'],
                'proposed_amount' => $item['proposed_amount'] ?? 0,
                'finalized_amount' => $item['approved_amount'] ?? 0,
            ];
        });

        return $data;

    }
}
